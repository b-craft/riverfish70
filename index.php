<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/store.php';

$data = riverfish_load();
$c = $data['siteContent'];
$products = riverfish_sorted_products($data);

$pageTitle = h($c['title']) . ' — свежая речная рыба';
$metaDesc = 'Свежий речной улов и рыба из с. Зырянское. Заказ в Telegram, VK и MAX.';
?>
<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= h($metaDesc) ?>" />
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
            boxShadow: {
              glow: '0 0 60px -12px rgba(234, 179, 8, 0.35)',
            },
          },
        },
      };
    </script>
  </head>
  <body class="min-h-screen bg-zinc-950 font-sans text-zinc-100 antialiased">
    <a
      href="#catalog"
      class="fixed left-4 top-4 z-[100] -translate-y-20 rounded-xl bg-yellow-500 px-4 py-2 text-sm font-bold text-zinc-950 opacity-0 transition focus:translate-y-0 focus:opacity-100"
      >К каталогу</a
    >

    <header
      class="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-zinc-950/75 backdrop-blur-md transition-colors"
      id="top"
    >
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 md:px-6">
        <a href="#top" class="text-lg font-extrabold tracking-tight text-white md:text-xl">
          <?= h($c['title']) ?><span class="text-yellow-400">.</span>
        </a>
        <nav class="hidden items-center gap-8 text-sm font-semibold text-zinc-300 md:flex">
          <a class="transition hover:text-white" href="#about">О нас</a>
          <a class="transition hover:text-white" href="#catalog">Каталог</a>
          <a class="transition hover:text-white" href="#order">Как заказать</a>
          <a class="transition hover:text-white" href="#contacts">Контакты</a>
        </nav>
        <div class="flex items-center gap-2">
          <a
            href="#catalog"
            class="hidden rounded-full bg-yellow-500 px-4 py-2 text-sm font-bold text-zinc-950 shadow-glow transition hover:bg-yellow-400 sm:inline-block"
            >В каталог</a
          >
        </div>
      </div>
    </header>

    <section class="relative isolate min-h-[min(88vh,720px)] overflow-hidden pt-16">
      <img
        src="https://images.unsplash.com/photo-1500375592092-40eb2168fd21?q=80&w=1920&auto=format&fit=crop"
        alt=""
        class="absolute inset-0 h-full w-full object-cover"
        fetchpriority="high"
      />
      <div
        class="absolute inset-0 bg-gradient-to-b from-zinc-950/20 via-zinc-950/75 to-zinc-950"
        aria-hidden="true"
      ></div>
      <div
        class="pointer-events-none absolute -left-24 top-1/3 h-72 w-72 rounded-full bg-yellow-500/15 blur-3xl"
        aria-hidden="true"
      ></div>

      <div class="relative z-10 mx-auto flex max-w-5xl flex-col items-center px-6 pb-24 pt-20 text-center md:pt-28">
        <p
          class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-yellow-300/90"
        >
          с. Зырянское · речной улов
        </p>
        <h1 class="text-balance text-4xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl">
          <?= h($c['title']) ?>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-pretty text-lg leading-relaxed text-zinc-200 md:text-xl">
          <?= h($c['subtitle']) ?>
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
          <a
            href="#catalog"
            class="inline-flex items-center justify-center rounded-2xl bg-yellow-500 px-8 py-3.5 text-base font-bold text-zinc-950 shadow-glow transition hover:-translate-y-0.5 hover:bg-yellow-400"
            >Смотреть каталог</a
          >
          <a
            href="https://t.me/riverfish70"
            class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/5 px-8 py-3.5 text-base font-semibold text-white backdrop-blur transition hover:border-white/40 hover:bg-white/10"
            >Написать в Telegram</a
          >
        </div>
        <dl
          class="mt-16 grid w-full max-w-3xl grid-cols-3 gap-4 text-left sm:gap-6"
        >
          <div class="rounded-2xl border border-white/10 bg-zinc-950/40 p-4 backdrop-blur sm:p-5">
            <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Свежесть</dt>
            <dd class="mt-1 text-2xl font-extrabold text-yellow-400 sm:text-3xl">100%</dd>
          </div>
          <div class="rounded-2xl border border-white/10 bg-zinc-950/40 p-4 backdrop-blur sm:p-5">
            <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Заказы</dt>
            <dd class="mt-1 text-2xl font-extrabold text-yellow-400 sm:text-3xl">24/7</dd>
          </div>
          <div class="rounded-2xl border border-white/10 bg-zinc-950/40 p-4 backdrop-blur sm:p-5">
            <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Регион</dt>
            <dd class="mt-1 text-2xl font-extrabold text-yellow-400 sm:text-3xl">70</dd>
          </div>
        </dl>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-20 md:py-28" id="about">
      <div class="grid gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl">
            Свежая речная рыба из с.&nbsp;Зырянское
          </h2>
          <p class="mt-6 text-lg leading-relaxed text-zinc-400">
            RiverFish70 — свежий улов, натуральная речная рыба и сезонные предложения без лишних
            посредников. Мы держим акцент на вкусе, текстуре и честной цене.
          </p>
          <ul class="mt-8 space-y-4 text-zinc-300">
            <li class="flex gap-3">
              <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-yellow-500"></span>
              <span>Улов и поставки из местных водоёмов — прозрачное происхождение продукта.</span>
            </li>
            <li class="flex gap-3">
              <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-yellow-500"></span>
              <span>Без длительной «логистики на складах»: стараемся отдавать рыбу максимально свежей.</span>
            </li>
            <li class="flex gap-3">
              <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-yellow-500"></span>
              <span>Удобный заказ в привычных мессенджерах — быстро согласуем состав и доставку.</span>
            </li>
          </ul>
        </div>
        <div
          class="group relative overflow-hidden rounded-[2.5rem] border border-zinc-800 bg-zinc-900 shadow-2xl ring-1 ring-white/5"
        >
          <img
            src="https://images.unsplash.com/photo-1498654896293-37aacf113fd9?q=80&w=1600&auto=format&fit=crop"
            alt="Свежая рыба"
            class="h-full min-h-[320px] w-full object-cover transition duration-700 group-hover:scale-[1.03]"
            loading="lazy"
          />
          <div
            class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-zinc-950/60 via-transparent to-transparent"
          ></div>
        </div>
      </div>
    </section>

    <section class="border-y border-zinc-800/80 bg-zinc-900/50 px-6 py-20 md:py-28">
      <div class="mx-auto max-w-6xl text-center">
        <h2 class="text-3xl font-extrabold sm:text-5xl">Почему нам доверяют</h2>
        <p class="mx-auto mt-6 max-w-3xl text-pretty text-lg leading-relaxed text-zinc-400">
          Мы занимаемся поставкой свежей речной рыбы и сезонного улова. Задача RiverFish70 — натуральный
          продукт без лишнего хранения и навязанных наценок.
        </p>
        <div class="mt-14 grid gap-6 md:grid-cols-3 md:gap-8">
          <article
            class="rounded-3xl border border-zinc-800 bg-zinc-950/80 p-8 text-left shadow-xl transition hover:-translate-y-1 hover:border-yellow-500/30 hover:shadow-glow"
          >
            <h3 class="text-xl font-bold text-yellow-400">Натуральный продукт</h3>
            <p class="mt-3 leading-relaxed text-zinc-400">Свежая речная рыба без «перезаморозки» и лишней обработки.</p>
          </article>
          <article
            class="rounded-3xl border border-zinc-800 bg-zinc-950/80 p-8 text-left shadow-xl transition hover:-translate-y-1 hover:border-yellow-500/30 hover:shadow-glow"
          >
            <h3 class="text-xl font-bold text-yellow-400">Прямые поставки</h3>
            <p class="mt-3 leading-relaxed text-zinc-400">Работаем напрямую — меньше звеньев между уловом и вашим столом.</p>
          </article>
          <article
            class="rounded-3xl border border-zinc-800 bg-zinc-950/80 p-8 text-left shadow-xl transition hover:-translate-y-1 hover:border-yellow-500/30 hover:shadow-glow"
          >
            <h3 class="text-xl font-bold text-yellow-400">Удобный заказ</h3>
            <p class="mt-3 leading-relaxed text-zinc-400">Telegram, VK и MAX — выбирайте удобный канал связи.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-20 md:py-28" id="catalog">
      <div class="mx-auto mb-14 max-w-2xl text-center">
        <h2 class="text-3xl font-extrabold sm:text-5xl">Каталог</h2>
        <p class="mt-4 text-lg text-zinc-400"><?= h($c['catalogText']) ?></p>
      </div>
      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($products as $product):
            $orderText = 'Здравствуйте! Хочу заказать: ' . $product['name'] . ' (' . $product['price'] . ')';
            $tg = 'https://t.me/riverfish70?text=' . rawurlencode($orderText);
            ?>
        <article
          class="group flex flex-col overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900/60 shadow-xl ring-1 ring-white/5 transition hover:-translate-y-1 hover:border-zinc-700 hover:shadow-2xl"
        >
          <div class="relative aspect-[4/3] overflow-hidden">
            <img
              src="<?= h($product['image']) ?>"
              alt="<?= h($product['name']) ?>"
              class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
              loading="lazy"
            />
            <?php if (!empty($product['isPromo']) && !empty($product['discount'])): ?>
            <span
              class="absolute left-4 top-4 rounded-full bg-red-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg"
            >
              <?= h($product['discount']) ?>
            </span>
            <?php endif; ?>
          </div>
          <div class="flex flex-1 flex-col p-6">
            <h3 class="text-xl font-bold text-white"><?= h($product['name']) ?></h3>
            <p class="mt-1 text-sm font-semibold text-yellow-500/90"><?= h($product['category']) ?></p>
            <p class="mt-3 flex-1 text-sm leading-relaxed text-zinc-400"><?= h($product['description']) ?></p>
            <div class="mt-5 flex flex-wrap items-baseline gap-2">
              <span class="text-2xl font-extrabold text-yellow-400"><?= h($product['price']) ?></span>
              <?php if (!empty($product['isPromo']) && !empty($product['oldPrice'])): ?>
              <span class="text-sm text-zinc-500 line-through"><?= h($product['oldPrice']) ?></span>
              <?php endif; ?>
            </div>
            <a
              href="<?= h($tg) ?>"
              class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-yellow-500 px-4 py-3 text-center text-sm font-bold text-zinc-950 transition hover:bg-yellow-400"
              >Заказать в Telegram</a
            >
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="bg-gradient-to-b from-zinc-900 to-zinc-950 px-6 py-20 md:py-28" id="order">
      <div class="mx-auto max-w-6xl">
        <div class="text-center">
          <h2 class="text-3xl font-extrabold sm:text-5xl">Как сделать заказ</h2>
          <p class="mx-auto mt-4 max-w-2xl text-zinc-400">Три простых шага — без лишних форм и ожидания на линии.</p>
        </div>
        <ol class="mt-14 grid gap-6 md:grid-cols-3">
          <li class="relative rounded-3xl border border-zinc-800 bg-zinc-950 p-8">
            <span class="text-5xl font-black text-yellow-500/20">1</span>
            <h3 class="mt-2 text-lg font-bold text-white">Выберите позицию</h3>
            <p class="mt-2 text-sm leading-relaxed text-zinc-400">Ознакомьтесь с каталогом и ценами на странице выше.</p>
          </li>
          <li class="relative rounded-3xl border border-zinc-800 bg-zinc-950 p-8">
            <span class="text-5xl font-black text-yellow-500/20">2</span>
            <h3 class="mt-2 text-lg font-bold text-white">Напишите нам</h3>
            <p class="mt-2 text-sm leading-relaxed text-zinc-400">Удобнее всего — кнопка «Заказать в Telegram» у товара.</p>
          </li>
          <li class="relative rounded-3xl border border-zinc-800 bg-zinc-950 p-8">
            <span class="text-5xl font-black text-yellow-500/20">3</span>
            <h3 class="mt-2 text-lg font-bold text-white">Подтвердим детали</h3>
            <p class="mt-2 text-sm leading-relaxed text-zinc-400">Согласуем объём, время и способ получения.</p>
          </li>
        </ol>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-16 md:py-24" id="contacts">
      <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900/40 p-8 lg:col-span-1">
          <h2 class="text-2xl font-extrabold">Контакты</h2>
          <p class="mt-3 text-sm leading-relaxed text-zinc-400">
            Свежий речной улов из с.&nbsp;Зырянское. Напишите — ответим как можно быстрее.
          </p>
          <a
            href="https://t.me/riverfish70"
            class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-yellow-500 py-3 text-sm font-bold text-zinc-950 transition hover:bg-yellow-400"
            >Telegram</a
          >
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:col-span-2 lg:grid-cols-2">
          <a
            href="https://vk.com/riverfish70"
            class="flex flex-col justify-between rounded-3xl border border-zinc-800 bg-zinc-950 p-8 transition hover:border-zinc-600"
          >
            <span class="text-sm font-semibold text-zinc-500">Сообщество</span>
            <span class="mt-4 text-xl font-bold text-white">ВКонтакте</span>
            <span class="mt-2 text-sm text-yellow-500">vk.com/riverfish70 →</span>
          </a>
          <a
            href="https://max.ru"
            class="flex flex-col justify-between rounded-3xl border border-zinc-800 bg-zinc-950 p-8 transition hover:border-zinc-600"
          >
            <span class="text-sm font-semibold text-zinc-500">Мессенджер</span>
            <span class="mt-4 text-xl font-bold text-white">MAX</span>
            <span class="mt-2 text-sm text-yellow-500">max.ru →</span>
          </a>
        </div>
      </div>
    </section>

    <footer class="border-t border-zinc-800 px-6 py-12 text-center text-zinc-500">
      <p class="text-lg font-bold text-white"><?= h($c['title']) ?></p>
      <p class="mt-2 text-sm">Свежий речной улов из с. Зырянское</p>
      <div class="mt-6 flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm font-semibold">
        <a class="text-zinc-400 transition hover:text-white" href="https://t.me/riverfish70">Telegram</a>
        <a class="text-zinc-400 transition hover:text-white" href="https://vk.com/riverfish70">VK</a>
        <a class="text-zinc-400 transition hover:text-white" href="https://max.ru">MAX</a>
        <a class="text-zinc-500 transition hover:text-zinc-300" href="cms.php">Редактирование сайта</a>
      </div>
    </footer>
  </body>
</html>
