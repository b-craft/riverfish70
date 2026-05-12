<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/store.php';

function riverfish_csrf_token(): string
{
    if (empty($_SESSION['riverfish_csrf'])) {
        $_SESSION['riverfish_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['riverfish_csrf'];
}

function riverfish_csrf_ok(): bool
{
    $t = $_POST['csrf'] ?? '';
    return is_string($t) && isset($_SESSION['riverfish_csrf']) && hash_equals($_SESSION['riverfish_csrf'], $t);
}

function riverfish_logged_in(): bool
{
    return !empty($_SESSION['riverfish_cms']);
}

$flashOk = '';
$flashErr = '';

if (isset($_GET['ok']) && riverfish_logged_in()) {
    if ($_GET['ok'] === 'site') {
        $flashOk = 'Настройки сайта сохранены.';
    } elseif ($_GET['ok'] === 'product') {
        $flashOk = 'Каталог обновлён: товар сохранён.';
    } elseif ($_GET['ok'] === 'del') {
        $flashOk = 'Товар удалён.';
    }
}

if (isset($_GET['export']) && riverfish_logged_in()) {
    $data = riverfish_load();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="riverfish70-backup.json"');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $pass = (string) ($_POST['password'] ?? '');
        if (hash_equals(RIVERFISH_CMS_PASSWORD, $pass)) {
            session_regenerate_id(true);
            $_SESSION['riverfish_cms'] = true;
            header('Location: cms.php');
            exit;
        }
        $flashErr = 'Неверный пароль.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: cms.php');
        exit;
    } elseif (riverfish_logged_in() && riverfish_csrf_ok()) {
        $data = riverfish_load();

        if (($_POST['action'] ?? '') === 'save_site') {
            $data['siteContent']['title'] = trim((string) ($_POST['title'] ?? ''));
            $data['siteContent']['subtitle'] = trim((string) ($_POST['subtitle'] ?? ''));
            $data['siteContent']['catalogText'] = trim((string) ($_POST['catalogText'] ?? ''));
            if (riverfish_save($data)) {
                header('Location: cms.php?ok=site');
                exit;
            }
            $flashErr = 'Не удалось записать файл data/site_data.json. Проверьте права на папку data/.';
        } elseif (($_POST['action'] ?? '') === 'save_product') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $category = trim((string) ($_POST['category'] ?? ''));
            $price = trim((string) ($_POST['price'] ?? ''));
            $image = trim((string) ($_POST['image'] ?? ''));
            if ($name === '' || $description === '' || $category === '' || $price === '' || $image === '') {
                $flashErr = 'Заполните обязательные поля товара: название, описание, категория, цена, ссылка на фото.';
            } else {
                $editing = (int) ($_POST['editing_id'] ?? 0);
                $row = [
                    'id' => $editing > 0 ? $editing : (int) (microtime(true) * 1000),
                    'name' => $name,
                    'description' => $description,
                    'category' => $category,
                    'price' => $price,
                    'oldPrice' => trim((string) ($_POST['oldPrice'] ?? '')),
                    'discount' => trim((string) ($_POST['discount'] ?? '')),
                    'image' => $image,
                    'isPromo' => !empty($_POST['isPromo']),
                ];
                $list = $data['products'] ?? [];
                if ($editing > 0) {
                    $found = false;
                    foreach ($list as $i => $p) {
                        if ((int) ($p['id'] ?? 0) === $editing) {
                            $list[$i] = riverfish_normalize_product($row);
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $list[] = riverfish_normalize_product($row);
                    }
                } else {
                    array_unshift($list, riverfish_normalize_product($row));
                }
                $data['products'] = $list;
                if (riverfish_save($data)) {
                    header('Location: cms.php?ok=product');
                    exit;
                }
                $flashErr = 'Ошибка записи данных.';
            }
        } elseif (($_POST['action'] ?? '') === 'delete_product') {
            $id = (int) ($_POST['product_id'] ?? 0);
            if ($id > 0) {
                $data['products'] = array_values(array_filter(
                    $data['products'] ?? [],
                    static fn(array $p): bool => (int) ($p['id'] ?? 0) !== $id
                ));
                if (riverfish_save($data)) {
                    header('Location: cms.php?ok=del');
                    exit;
                }
            }
            $flashErr = 'Не удалось удалить товар.';
        }
    } elseif (isset($_POST['action']) && riverfish_logged_in() && !riverfish_csrf_ok()) {
        $flashErr = 'Сессия устарела. Обновите страницу и попробуйте снова.';
    }
}

$data = riverfish_load();
$c = $data['siteContent'];
$products = riverfish_sorted_products($data);

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$form = [
    'id' => 0,
    'name' => '',
    'description' => '',
    'category' => '',
    'price' => '',
    'oldPrice' => '',
    'discount' => '',
    'image' => '',
    'isPromo' => false,
];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_product' && $flashErr !== '') {
    $form = riverfish_normalize_product([
        'id' => (int) ($_POST['editing_id'] ?? 0),
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'category' => $_POST['category'] ?? '',
        'price' => $_POST['price'] ?? '',
        'oldPrice' => $_POST['oldPrice'] ?? '',
        'discount' => $_POST['discount'] ?? '',
        'image' => $_POST['image'] ?? '',
        'isPromo' => !empty($_POST['isPromo']),
    ]);
} elseif ($editId > 0) {
    foreach ($data['products'] ?? [] as $p) {
        if ((int) ($p['id'] ?? 0) === $editId) {
            $form = riverfish_normalize_product($p);
            break;
        }
    }
}

$csrf = riverfish_csrf_token();
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex,nofollow" />
    <title>CMS — <?= h($c['title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { fontFamily: { sans: ['Manrope', 'system-ui', 'sans-serif'] } } } };
    </script>
  </head>
  <body class="min-h-screen bg-zinc-950 font-sans text-zinc-100">
    <?php if (!riverfish_logged_in()): ?>
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-16">
      <p class="text-center text-sm font-semibold uppercase tracking-widest text-yellow-500">RiverFish70</p>
      <h1 class="mt-2 text-center text-3xl font-extrabold">Вход в CMS</h1>
      <p class="mt-2 text-center text-sm text-zinc-400">Пароль задаётся в файле <code class="text-yellow-500/90">config.php</code></p>
      <?php if ($flashErr !== ''): ?>
      <div class="mt-6 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
        <?= h($flashErr) ?>
      </div>
      <?php endif; ?>
      <form method="post" class="mt-8 space-y-4 rounded-3xl border border-zinc-800 bg-zinc-900 p-8 shadow-xl">
        <input type="hidden" name="action" value="login" />
        <label class="block text-sm font-semibold text-zinc-300">Пароль</label>
        <input
          type="password"
          name="password"
          autocomplete="current-password"
          required
          class="w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none ring-yellow-500/30 focus:ring-2"
        />
        <button
          type="submit"
          class="w-full rounded-2xl bg-yellow-500 py-3 text-sm font-bold text-zinc-950 transition hover:bg-yellow-400"
        >
          Войти
        </button>
      </form>
      <p class="mt-8 text-center text-sm text-zinc-500">
        <a href="index.php" class="font-semibold text-yellow-500 hover:text-yellow-400">← На сайт</a>
      </p>
    </div>
    <?php else: ?>

    <header class="border-b border-zinc-800 bg-zinc-900/80 backdrop-blur">
      <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">
        <div>
          <h1 class="text-xl font-extrabold text-yellow-400">CMS RiverFish70</h1>
          <p class="text-xs text-zinc-500">Данные: <span class="font-mono text-zinc-400">data/site_data.json</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <a
            href="index.php"
            class="rounded-xl border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-zinc-500"
            target="_blank"
            >Открыть сайт</a
          >
          <a
            href="cms.php?export=1"
            class="rounded-xl border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-zinc-500"
            >Скачать JSON</a
          >
          <form method="post" class="inline">
            <input type="hidden" name="action" value="logout" />
            <button
              type="submit"
              class="rounded-xl bg-zinc-800 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700"
            >
              Выйти
            </button>
          </form>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10">
      <?php if ($flashOk !== ''): ?>
      <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        <?= h($flashOk) ?>
      </div>
      <?php endif; ?>
      <?php if ($flashErr !== ''): ?>
      <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
        <?= h($flashErr) ?>
      </div>
      <?php endif; ?>

      <div class="grid gap-10 lg:grid-cols-2">
        <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-8 shadow-xl">
          <h2 class="text-xl font-bold text-white">Тексты на главной</h2>
          <form method="post" class="mt-6 space-y-4">
            <input type="hidden" name="action" value="save_site" />
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>" />
            <div>
              <label class="text-sm font-semibold text-zinc-400">Заголовок (название)</label>
              <input
                name="title"
                value="<?= h($c['title']) ?>"
                class="mt-1 w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
            </div>
            <div>
              <label class="text-sm font-semibold text-zinc-400">Подзаголовок</label>
              <input
                name="subtitle"
                value="<?= h($c['subtitle']) ?>"
                class="mt-1 w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
            </div>
            <div>
              <label class="text-sm font-semibold text-zinc-400">Текст под «Каталог»</label>
              <input
                name="catalogText"
                value="<?= h($c['catalogText']) ?>"
                class="mt-1 w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
            </div>
            <button
              type="submit"
              class="rounded-2xl bg-yellow-500 px-6 py-3 text-sm font-bold text-zinc-950 hover:bg-yellow-400"
            >
              Сохранить тексты
            </button>
          </form>
        </section>

        <section class="rounded-3xl border border-yellow-500/40 bg-zinc-900 p-8 shadow-xl ring-1 ring-yellow-500/20">
          <h2 class="text-xl font-bold text-white"><?= $form['id'] > 0 ? 'Редактирование товара' : 'Новый товар' ?></h2>
          <form method="post" class="mt-6 grid gap-4">
            <input type="hidden" name="action" value="save_product" />
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>" />
            <input type="hidden" name="editing_id" value="<?= $form['id'] > 0 ? (int) $form['id'] : 0 ?>" />
            <input
              name="name"
              value="<?= h($form['name']) ?>"
              placeholder="Название *"
              required
              class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
            />
            <textarea
              name="description"
              rows="3"
              placeholder="Описание *"
              required
              class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
            ><?= h($form['description']) ?></textarea>
            <input
              name="category"
              value="<?= h($form['category']) ?>"
              placeholder="Категория *"
              required
              class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
            />
            <div class="grid gap-4 sm:grid-cols-2">
              <input
                name="price"
                value="<?= h($form['price']) ?>"
                placeholder="Цена *"
                required
                class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
              <input
                name="oldPrice"
                value="<?= h($form['oldPrice']) ?>"
                placeholder="Старая цена"
                class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <input
                name="discount"
                value="<?= h($form['discount']) ?>"
                placeholder="Скидка (например -20%)"
                class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
              <input
                name="image"
                value="<?= h($form['image']) ?>"
                placeholder="URL фото *"
                required
                class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 outline-none focus:ring-2 focus:ring-yellow-500/40"
              />
            </div>
            <label class="flex items-center gap-3 rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3">
              <input type="checkbox" name="isPromo" value="1" <?= !empty($form['isPromo']) ? 'checked' : '' ?> />
              <span class="text-sm font-medium">Акционный товар</span>
            </label>
            <div class="flex flex-wrap gap-3">
              <button
                type="submit"
                class="rounded-2xl bg-yellow-500 px-6 py-3 text-sm font-bold text-zinc-950 hover:bg-yellow-400"
              >
                <?= $form['id'] > 0 ? 'Сохранить изменения' : 'Добавить товар' ?>
              </button>
              <?php if ($form['id'] > 0): ?>
              <a
                href="cms.php"
                class="inline-flex items-center rounded-2xl border border-zinc-600 px-6 py-3 text-sm font-semibold hover:border-zinc-400"
                >Отмена</a
              >
              <?php endif; ?>
            </div>
          </form>
        </section>
      </div>

      <section class="mt-12 overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900 shadow-xl">
        <div class="border-b border-zinc-800 px-6 py-4">
          <h2 class="text-lg font-bold">Товары в каталоге</h2>
          <p class="text-xs text-zinc-500">Сначала новые по внутреннему id</p>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-zinc-800 text-left text-sm">
            <thead class="bg-zinc-950/80 text-xs font-bold uppercase tracking-wide text-zinc-500">
              <tr>
                <th class="px-4 py-3">Фото</th>
                <th class="px-4 py-3">Название</th>
                <th class="px-4 py-3">Цена</th>
                <th class="px-4 py-3 text-right">Действия</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
              <?php foreach ($products as $p): ?>
              <tr class="hover:bg-zinc-800/40">
                <td class="px-4 py-3">
                  <img src="<?= h($p['image']) ?>" alt="" class="h-12 w-16 rounded-lg object-cover" loading="lazy" />
                </td>
                <td class="px-4 py-3 font-semibold text-white"><?= h($p['name']) ?></td>
                <td class="px-4 py-3 text-zinc-400"><?= h($p['price']) ?></td>
                <td class="px-4 py-3 text-right">
                  <a
                    href="cms.php?edit=<?= (int) $p['id'] ?>"
                    class="mr-2 inline-block rounded-lg bg-yellow-500/90 px-3 py-1.5 text-xs font-bold text-zinc-950 hover:bg-yellow-400"
                    >Изменить</a
                  >
                  <form method="post" class="inline" onsubmit="return confirm('Удалить этот товар?');">
                    <input type="hidden" name="action" value="delete_product" />
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>" />
                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>" />
                    <button
                      type="submit"
                      class="rounded-lg bg-red-500/90 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-500"
                    >
                      Удалить
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
    <?php endif; ?>
  </body>
</html>
