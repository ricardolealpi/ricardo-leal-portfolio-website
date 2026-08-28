# 🚀 ricardoleal.cloud — Professional Portfolio & Tech Platform

Sitio web personal y portafolio técnico enfocado en **Cloud Infrastructure**, **System Administration** y **DevOps Automation**. Construido sobre un tema hijo personalizado de GeneratePress con un sistema de diseño inspirado en consolas cloud y el modo oscuro de GitHub.

## 🛠️ Stack Tecnológico & Arquitectura

* **CMS & Core:** WordPress + Custom GeneratePress Child Theme (v1.1.2)
* **Layout & Bloques:** GenerateBlocks (con vinculación de *Dynamic Data* nativa)
* **Estilos & UI System:** CSS3 nativo, Variables CSS (`:root`), Tipografía fluida con `clamp()`, Modo Oscuro/Claro dinámico y cápsulas técnicas agnósticas (`> *`).
* **Entorno de Desarrollo:** macOS / Staging remoto (`staging.ricardoleal.cloud`)
* **Control de Versiones & CI/CD:** Git, GitHub, **GitHub Actions** (Despliegues automáticos vía SFTP a staging/producción).
* **Rendimiento & UX:** Filtrado interactivo vía AJAX (*Filter Everything*), cero impacto en CLS (Cumulative Layout Shift) y optimización de Core Web Vitals.

## 📁 Estructura del Tema Hijo

```text
generatepress-child/
├── style.css             # Arquitectura CSS (Tipografía fluida, Dark Mode, Tarjetas y Badges)
├── functions.php         # Encolado de recursos, fuentes y funciones personalizadas
├── .github/
│   └── workflows/        # Pipeline de integración y despliegue continuo (CI/CD)
└── README.md             # Documentación técnica del proyecto