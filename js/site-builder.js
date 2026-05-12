/**
 * RiverFish70 — общая схема «конструктора» для index.html и cms.html
 */
;(function (global) {
  var DEFAULT_PRODUCTS = [
    {
      id: 1,
      name: 'Щука речная',
      description: 'Свежая речная щука из местных водоёмов.',
      category: 'Речная рыба',
      price: '650 ₽/кг',
      oldPrice: '800 ₽/кг',
      discount: '-20%',
      isPromo: true,
      image:
        'https://images.unsplash.com/photo-1510130387422-82bed34b37e9?q=80&w=1200&auto=format&fit=crop',
    },
    {
      id: 2,
      name: 'Окунь речной',
      description: 'Свежий окунь из с. Зырянское.',
      category: 'Речная рыба',
      price: '850 ₽/кг',
      oldPrice: '1000 ₽/кг',
      discount: '-15%',
      isPromo: true,
      image:
        'https://images.unsplash.com/photo-1544943910-4c1dc44aab44?q=80&w=1200&auto=format&fit=crop',
    },
    {
      id: 3,
      name: 'Свежий улов',
      description: 'Свежий сезонный улов.',
      category: 'Свежий улов',
      price: '500 ₽',
      oldPrice: '',
      discount: '',
      isPromo: false,
      image:
        'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=1200&auto=format&fit=crop',
    },
  ]

  var DEFAULT_SITE = {
    version: 2,
    media: {
      folder: 'media',
    },
    theme: {
      accent: '#eab308',
    },
    seo: {
      titleSuffix: ' — свежая речная рыба',
      metaDescription:
        'Свежий речной улов и рыба из с. Зырянское. Заказ в Telegram, VK и MAX.',
    },
    brand: {
      name: 'RIVERFISH70',
      logoMark: '.',
    },
    nav: [
      { label: 'О нас', href: '#about' },
      { label: 'Каталог', href: '#catalog' },
      { label: 'Как заказать', href: '#order' },
      { label: 'Контакты', href: '#contacts' },
    ],
    hero: {
      badge: 'с. Зырянское · речной улов',
      title: '',
      subtitle: 'Свежая речная рыба и улов из с. Зырянское',
      bgImage:
        'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?q=80&w=1920&auto=format&fit=crop',
      ctaPrimary: { label: 'Смотреть каталог', href: '#catalog' },
      ctaSecondary: { label: 'Написать в Telegram', href: 'https://t.me/riverfish70' },
      stats: [
        { label: 'Свежесть', value: '100%' },
        { label: 'Заказы', value: '24/7' },
        { label: 'Регион', value: '70' },
      ],
    },
    about: {
      title: 'Свежая речная рыба из с.\u00a0Зырянское',
      lead:
        'RiverFish70 — свежий улов, натуральная речная рыба и сезонные предложения без лишних посредников. Мы держим акцент на вкусе, текстуре и честной цене.',
      bullets: [
        'Улов и поставки из местных водоёмов — прозрачное происхождение продукта.',
        'Без длительной «логистики на складах»: стараемся отдавать рыбу максимально свежей.',
        'Удобный заказ в привычных мессенджерах — быстро согласуем состав и доставку.',
      ],
      image:
        'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?q=80&w=1600&auto=format&fit=crop',
      imageAlt: 'Свежая рыба',
    },
    trust: {
      title: 'Почему нам доверяют',
      lead:
        'Мы занимаемся поставкой свежей речной рыбы и сезонного улова. Задача RiverFish70 — натуральный продукт без лишнего хранения и навязанных наценок.',
      cards: [
        {
          title: 'Натуральный продукт',
          text: 'Свежая речная рыба без «перезаморозки» и лишней обработки.',
        },
        {
          title: 'Прямые поставки',
          text: 'Работаем напрямую — меньше звеньев между уловом и вашим столом.',
        },
        {
          title: 'Удобный заказ',
          text: 'Telegram, VK и MAX — выбирайте удобный канал связи.',
        },
      ],
    },
    catalog: {
      title: 'Каталог',
      intro: 'Натуральная речная рыба и свежий улов',
    },
    order: {
      title: 'Как сделать заказ',
      subtitle: 'Три простых шага — без лишних форм и ожидания на линии.',
      steps: [
        {
          title: 'Выберите позицию',
          text: 'Ознакомьтесь с каталогом и ценами на странице выше.',
        },
        { title: 'Напишите нам', text: 'Удобнее всего — кнопка «Заказать в Telegram» у товара.' },
        { title: 'Подтвердим детали', text: 'Согласуем объём, время и способ получения.' },
      ],
      telegramUser: 'riverfish70',
    },
    contacts: {
      title: 'Контакты',
      text: 'Свежий речной улов из с.\u00a0Зырянское. Напишите — ответим как можно быстрее.',
      telegramCtaLabel: 'Telegram',
      telegramHref: 'https://t.me/riverfish70',
      cards: [
        {
          kicker: 'Сообщество',
          title: 'ВКонтакте',
          hint: 'vk.com/riverfish70 →',
          href: 'https://vk.com/riverfish70',
        },
        {
          kicker: 'Мессенджер',
          title: 'MAX',
          hint: 'max.ru →',
          href: 'https://max.ru',
        },
      ],
    },
    footer: {
      tagline: 'Свежий речной улов из с. Зырянское',
      cmsLabel: 'Редактирование сайта',
      cmsHref: 'cms.html',
      links: [
        { label: 'Telegram', href: 'https://t.me/riverfish70' },
        { label: 'VK', href: 'https://vk.com/riverfish70' },
        { label: 'MAX', href: 'https://max.ru' },
      ],
    },
  }

  function deepCopy(x) {
    return JSON.parse(JSON.stringify(x))
  }

  function isObj(x) {
    return x && typeof x === 'object' && !Array.isArray(x)
  }

  function deepMerge(base, patch) {
    if (!isObj(base)) base = {}
    if (!isObj(patch)) return deepCopy(base)
    var out = deepCopy(base)
    Object.keys(patch).forEach(function (k) {
      var pv = patch[k]
      if (Array.isArray(pv)) {
        out[k] = pv.slice()
      } else if (isObj(pv) && isObj(out[k])) {
        out[k] = deepMerge(out[k], pv)
      } else if (pv !== undefined) {
        out[k] = pv
      }
    })
    return out
  }

  function syncSiteContent(state) {
    var site = state.site || DEFAULT_SITE
    state.siteContent = {
      title: site.brand && site.brand.name ? site.brand.name : DEFAULT_SITE.brand.name,
      subtitle: site.hero && site.hero.subtitle ? site.hero.subtitle : DEFAULT_SITE.hero.subtitle,
      catalogText:
        site.catalog && site.catalog.intro ? site.catalog.intro : DEFAULT_SITE.catalog.intro,
    }
  }

  function migrate(raw) {
    var out = {
      site: deepCopy(DEFAULT_SITE),
      products: DEFAULT_PRODUCTS.slice(),
      siteContent: {},
    }
    if (!raw || typeof raw !== 'object') {
      syncSiteContent(out)
      return out
    }
    out.products =
      Array.isArray(raw.products) && raw.products.length ? raw.products : DEFAULT_PRODUCTS.slice()

    if (raw.site && raw.site.version >= 2) {
      out.site = deepMerge(DEFAULT_SITE, raw.site)
    } else {
      out.site = deepCopy(DEFAULT_SITE)
      var sc = raw.siteContent || {}
      if (sc.title) {
        out.site.brand.name = String(sc.title)
        out.site.hero.title = String(sc.title)
      }
      if (sc.subtitle) out.site.hero.subtitle = String(sc.subtitle)
      if (sc.catalogText) out.site.catalog.intro = String(sc.catalogText)
    }

    syncSiteContent(out)
    return out
  }

  function normMediaFolder(site) {
    var f = site && site.media && site.media.folder
    f = String(f != null ? f : 'media')
      .trim()
      .replace(/\\/g, '/')
      .replace(/\/+$/, '')
    return f || 'media'
  }

  function resolveMediaUrl(site, url) {
    var u = String(url || '').trim()
    if (!u) return ''
    var low = u.toLowerCase()
    if (
      low.indexOf('http://') === 0 ||
      low.indexOf('https://') === 0 ||
      low.indexOf('//') === 0 ||
      low.indexOf('data:') === 0 ||
      low.indexOf('blob:') === 0
    ) {
      return u
    }
    if (u.charAt(0) === '/') return u
    var folder = normMediaFolder(site)
    var rel = u
      .replace(/^\.?\//, '')
      .replace(/\\/g, '/')
    rel = rel
      .split('/')
      .filter(function (p) {
        return p && p !== '.' && p !== '..'
      })
      .join('/')
    if (!rel) return ''
    var prefix = folder + '/'
    if (rel.toLowerCase().indexOf(prefix.toLowerCase()) === 0) return rel
    return prefix + rel
  }

  function telegramOrderUrl(site, productName, price) {
    var u = (site && site.order && site.order.telegramUser) || 'riverfish70'
    u = String(u).replace(/^@/, '')
    var text = 'Здравствуйте! Хочу заказать: ' + productName + ' (' + price + ')'
    return 'https://t.me/' + u + '?text=' + encodeURIComponent(text)
  }

  function applyTheme(site) {
    var accent = (site && site.theme && site.theme.accent) || '#eab308'
    document.documentElement.style.setProperty('--rf-accent', accent)
  }

  global.RiverFishBuilder = {
    DEFAULT_SITE: DEFAULT_SITE,
    DEFAULT_PRODUCTS: DEFAULT_PRODUCTS,
    deepMerge: deepMerge,
    migrate: migrate,
    syncSiteContent: syncSiteContent,
    telegramOrderUrl: telegramOrderUrl,
    applyTheme: applyTheme,
    resolveMediaUrl: resolveMediaUrl,
    normMediaFolder: normMediaFolder,
  }
})(typeof window !== 'undefined' ? window : this)
