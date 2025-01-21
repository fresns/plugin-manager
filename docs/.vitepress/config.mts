import { defineConfig } from 'vitepress'

export default defineConfig({
    lang: 'en-US',
    title: 'Plugin Manager',
    description: 'Plugin Manager is a convenient Laravel extension package designed for modular management of your large-scale Laravel applications. Each plugin acts as an independent Laravel application or microservice, allowing you to define your own views, controllers and models.',

    lastUpdated: true,

    head: [
        ['link', { rel: 'icon', type: 'image/png', href: 'https://assets.fresns.com/images/icons/pm.png' }],
    ],

    themeConfig: {
        logo: 'https://assets.fresns.com/images/icons/pm.png',

        nav: [
            { text: 'Home', link: '/' },
            { text: 'Guide', link: '/guide/' },
            { text: 'Artisan', link: '/artisan/' },
            { text: 'Command Word', link: '/command-word/' },
            { text: 'DTO', link: '/dto/' },
        ],

        footer: {
            message: 'Released under the Apache-2.0 License',
            copyright: 'Copyright © 2021-2025 <a href="https://github.com/jevantang" target="_blank">Jevan Tang</a>',
        },

        editLink: {
            pattern: 'https://github.com/fresns/plugin-manager/tree/2.x/docs/:path',
            text: 'Suggest changes to this page'
        },

        sidebar: [
            {
                text: 'Guide',
                collapsed: false,
                items: [
                    { text: 'Introduction', link: '/guide/' },
                    { text: 'Installation and Setup', link: '/guide/installation.md' },
                    { text: 'Plugin Structure', link: '/guide/structure.md' },
                    { text: 'Use Cases', link: '/guide/use-cases.md' },
                ]
            },
            {
                text: 'Artisan',
                collapsed: false,
                items: [
                    { text: 'Overview', link: '/artisan/' },
                    { text: 'Usage Flow', link: '/artisan/started.md' },
                    { text: 'Generate', link: '/artisan/create.md' },
                    { text: 'Development', link: '/artisan/development.md' },
                    { text: 'Control', link: '/artisan/control.md' },
                    { text: 'Management', link: '/artisan/management.md' },
                ]
            },
            {
                text: 'Command Word Manager',
                collapsed: false,
                items: [
                    { text: 'Introduction', link: '/command-word/index.md' },
                    { text: 'Cmd Word Dev', link: '/command-word/development.md' },
                    { text: 'Cmd Word Usage', link: '/command-word/usage.md' },
                ]
            },
            {
                text: 'DTO',
                collapsed: false,
                items: [
                    { text: 'Introduction', link: '/dto/index.md' },
                ]
            }
        ],

        search: {
            provider: 'local',
            options: {
                locales: {
                    'zh-hans': {
                        translations: {
                            button: {
                                buttonText: '搜索文档',
                                buttonAriaLabel: '搜索文档'
                            },
                            modal: {
                                noResultsText: '无法找到相关结果',
                                resetButtonTitle: '清除查询条件',
                                footer: {
                                    selectText: '选择',
                                    navigateText: '切换',
                                    closeText: '关闭',
                                }
                            }
                        }
                    }
                }
            }
        },

        socialLinks: [
            { icon: 'github', link: 'https://github.com/fresns/plugin-manager' }
        ]
    },

    locales: {
        root: {
            label: 'English',
            lang: 'en-US'
        },
        'zh-hant': {
            label: '繁體中文',
            lang: 'zh-Hant',
            description: '外掛管理器是一個便捷的 Laravel 擴充包，用於模組化管理您的龐大 Laravel 應用程式。每個外掛就像一個獨立的 Laravel 應用或者微服務，可以定義自己的視圖、控制器和模型。',
            themeConfig: {
                nav: [
                    { text: '首頁', link: '/zh-hant/' },
                    { text: '指南', link: '/zh-hant/guide/', activeMatch: '/zh-hant/guide/' },
                    { text: '外掛指令', link: '/zh-hant/artisan/', activeMatch: '/zh-hant/artisan/' },
                    { text: '指令字', link: '/zh-hant/command-word/', activeMatch: '/zh-hant/command-word/' },
                    { text: 'DTO', link: '/zh-hant/dto/', activeMatch: '/zh-hant/dto/' },
                ],

                footer: {
                    message: '遵循 Apache-2.0 開源協定',
                    copyright: 'Copyright © 2021-2025 <a href="https://github.com/jevantang" target="_blank">Jevan Tang</a>',
                },

                outlineTitle: '本頁導覽',
                returnToTopLabel: '回到頂部',
                sidebarMenuLabel: '選單',
                darkModeSwitchLabel: '深色模式',

                editLink: {
                    pattern: 'https://github.com/fresns/plugin-manager/tree/2.x/docs/:path',
                    text: '為此頁提供修改建議'
                },

                lastUpdatedText: '最後一次更新',

                docFooter: {
                    prev: '上一項',
                    next: '下一項',
                },

                sidebar: [
                    {
                        text: '使用指南',
                        collapsed: false,
                        items: [
                            { text: '介紹', link: '/zh-hant/guide/' },
                            { text: '安裝和配置', link: '/zh-hant/guide/installation.md' },
                            { text: '外掛結構', link: '/zh-hant/guide/structure.md' },
                            { text: '使用案例', link: '/zh-hant/guide/use-cases.md' },
                        ]
                    },
                    {
                        text: '外掛指令',
                        collapsed: false,
                        items: [
                            { text: '總覽', link: '/zh-hant/artisan/' },
                            { text: '使用流程', link: '/zh-hant/artisan/started.md' },
                            { text: '創建新外掛', link: '/zh-hant/artisan/create.md' },
                            { text: '開發指令', link: '/zh-hant/artisan/development.md' },
                            { text: '控制指令', link: '/zh-hant/artisan/control.md' },
                            { text: '管理指令', link: '/zh-hant/artisan/management.md' },
                        ]
                    },
                    {
                        text: '指令字管理器',
                        collapsed: false,
                        items: [
                            { text: '介紹', link: '/zh-hant/command-word/index.md' },
                            { text: '指令字開發', link: '/zh-hant/command-word/development.md' },
                            { text: '指令字使用', link: '/zh-hant/command-word/usage.md' },
                        ]
                    },
                    {
                        text: 'DTO',
                        collapsed: false,
                        items: [
                            { text: '介紹', link: '/zh-hant/dto/index.md' },
                        ]
                    }
                ],
            }
        },
        'zh-hans': {
            label: '简体中文',
            lang: 'zh-Hans',
            description: '插件管理器是一个便捷的 Laravel 扩展包，用于模块化管理您的庞大 Laravel 应用程序。每个插件就像一个独立的 Laravel 应用或者微服务，可以定义自己的视图、控制器和模型。',
            themeConfig: {
                nav: [
                    { text: '首页', link: '/zh-hans/' },
                    { text: '指南', link: '/zh-hans/guide/', activeMatch: '/zh-hans/guide/' },
                    { text: '插件指令', link: '/zh-hans/artisan/', activeMatch: '/zh-hans/artisan/' },
                    { text: '命令字', link: '/zh-hans/command-word/', activeMatch: '/zh-hans/command-word/' },
                    { text: 'DTO', link: '/zh-hans/dto/', activeMatch: '/zh-hans/dto/' },
                ],

                footer: {
                    message: '遵循 Apache-2.0 开源协议',
                    copyright: 'Copyright © 20215 <a href="https://github.com/jevantang" target="_blank">唐杰</a>',
                },

                outlineTitle: '本页导览',
                returnToTopLabel: '返回顶部',
                sidebarMenuLabel: '菜单',
                darkModeSwitchLabel: '深色模式',

                editLink: {
                    pattern: 'https://github.com/fresns/plugin-manager/tree/2.x/docs/:path',
                    text: '为此页提供修改建议'
                },

                lastUpdatedText: '最后一次更新',

                docFooter: {
                    prev: '上一项',
                    next: '下一项',
                },

                sidebar: [
                    {
                        text: '使用指南',
                        collapsed: false,
                        items: [
                            { text: '介绍', link: '/zh-hans/guide/' },
                            { text: '安装和配置', link: '/zh-hans/guide/installation.md' },
                            { text: '插件结构', link: '/zh-hans/guide/structure.md' },
                            { text: '使用案例', link: '/zh-hans/guide/use-cases.md' },
                        ]
                    },
                    {
                        text: '插件指令',
                        collapsed: false,
                        items: [
                            { text: '总览', link: '/zh-hans/artisan/' },
                            { text: '使用流程', link: '/zh-hans/artisan/started.md' },
                            { text: '创建新插件', link: '/zh-hans/artisan/create.md' },
                            { text: '开发指令', link: '/zh-hans/artisan/development.md' },
                            { text: '控制指令', link: '/zh-hans/artisan/control.md' },
                            { text: '管理指令', link: '/zh-hans/artisan/management.md' },
                        ]
                    },
                    {
                        text: '命令字管理器',
                        collapsed: false,
                        items: [
                            { text: '介绍', link: '/zh-hans/command-word/index.md' },
                            { text: '命令字开发', link: '/zh-hans/command-word/development.md' },
                            { text: '命令字使用', link: '/zh-hans/command-word/usage.md' },
                        ]
                    },
                    {
                        text: 'DTO',
                        collapsed: false,
                        items: [
                            { text: '介绍', link: '/zh-hans/dto/index.md' },
                        ]
                    }
                ],
            }
        }
    }
})
