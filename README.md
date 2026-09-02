# 🚀 ricardoleal.cloud — Professional Portfolio & Tech Platform

Sitio web personal y portafolio técnico enfocado en Cloud Infrastructure, System Administration y DevOps Automation. Construido sobre un tema hijo personalizado de GeneratePress con un sistema de diseño inspirado en consolas cloud y el modo oscuro por defecto de GitHub.

## 🛠️ Stack Tecnológico & Arquitectura

* **CMS & Core:** WordPress + Custom GeneratePress Child Theme (v1.1.5).
* **Layout & Bloques:** Gutenberg + GenerateBlocks (diseño basado en patrones y componentes).
* **UI/UX System:** CSS3 nativo (`:root`), variables dinámicas, tipografía fluida (`clamp()`), modo oscuro/claro dinámico y micro-interacciones hover (elevación + resplandor).
* **Backend PHP Custom:** Integración con GitHub REST API vía shortcode personalizado (`[github_stars]`), inyección explícita de `postId` en Query Loops y almacenamiento en caché con `transients` (12h TTL + purging automático en `save_post`).
* **Frontend Scripting:** Prevención de FOUC (Flash of Unstyled Content) en `<head>` mediante `localStorage` y listener para conmutador de tema.
* **Control de Versiones & CI/CD:** Git, GitHub, GitHub Actions (pipelines para despliegue automatizado vía SFTP a staging/producción).
* **Cross-Browser & SEO:** Fixes específicos de renderizado Flexbox para WebKit (Safari), supresión de clics en badges dinámicos (`pointer-events: none`) y control de indexación (`noindex`) en páginas de taxonomías para evitar contenido duplicado.

## 📁 Estructura del Tema Hijo

generatepress-child/
├── style.css             # Arquitectura CSS (Variables, Dark/Light Mode, Safari fixes, Badges)
├── functions.php         # Shortcode GitHub API, Transient Cache, Hooks GP, Dark Mode scripts
├── .github/
│   └── workflows/        # Pipeline de CI/CD con GitHub Actions (Despliegue automatizado)
└── README.md             # Documentación técnica del proyecto