module.exports = {
  base: '/docs/',
  dest: 'public/docs',
  locales: {
    '/': {
      lang: 'zh-CN',
      title: '喜歌云文档',
      description: '喜歌云后台接口文档'
    }
  },
  head: [
    ['link', { rel: 'icon', href: `/logo.png` }],
    ['link', { rel: 'manifest', href: '/manifest.json' }],
    ['meta', { name: 'theme-color', content: '#3eaf7c' }],
    ['meta', { name: 'apple-mobile-web-app-capable', content: 'yes' }],
    ['meta', { name: 'apple-mobile-web-app-status-bar-style', content: 'black' }],
    ['link', { rel: 'apple-touch-icon', href: `/icons/apple-touch-icon-152x152.png` }],
    ['link', { rel: 'mask-icon', href: '/icons/safari-pinned-tab.svg', color: '#3eaf7c' }],
    ['meta', { name: 'msapplication-TileImage', content: '/icons/msapplication-icon-144x144.png' }],
    ['meta', { name: 'msapplication-TileColor', content: '#000000' }]
  ],
  serviceWorker: true,
  themeConfig: {
    // editLinks: true,
    docsDir: 'docs',
    locales: {
      '/': {
        label: '简体中文',
        selectText: '选择语言',
        editLinkText: '编辑此页',
        lastUpdated: '上次更新',
        nav: [
          {
            text: 'API参考',
            link: '/api/',
          },
          {
            text: '指南',
            link: '/guide/',
          },
          {
            text: '配置参考',
            link: '/config/'
          },
          {
            text: '默认主题配置',
            link: '/default-theme-config/'
          },
        ],
        sidebar: {
          '/api/': genSidebarConfig('API参考'),
          '/guide/': genGuideSidebarConfig('指南'),
        }
      }
    }
  }
}

function genSidebarConfig (title) {
  return [
    {
      title,
      collapsable: false,
      children: [
        '',
        'categories',
        'roles',
        'tags'
      ]
    }
  ]
}

function genGuideSidebarConfig (title) {
  return [
    {
      title,
      collapsable: false,
      children: [
        '',
        'getting-started',
        'basic-config',
        'assets',
        'markdown',
        'using-vue',
        'custom-themes',
        'i18n',
        'deploy'
      ]
    }
  ]
}
