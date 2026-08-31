<?php
/**
 * Ricardo Leal Portfolio functions and definitions
 */

add_action( 'wp_enqueue_scripts', 'ricardo_leal_enqueue_styles', 10 );
function ricardo_leal_enqueue_styles() {
    // Carga el estilo del tema padre (GeneratePress)
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    
    // Carga el estilo de este tema hijo
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ), '1.0.0' );
}

// 1. Quitar autor/correo SOLO en entradas de la categoría "portafolio"
add_filter( 'generate_header_entry_meta_items', function( $items ) {
    if ( is_single() && in_category( 'portafolio' ) ) {
        return array( 'date' ); // Mantiene únicamente la fecha en el portafolio
    }
    return $items; // El blog conserva autor, fecha, etc.
} );

// 2. Eliminar categorías y etiquetas al final SOLO en el portafolio
add_filter( 'generate_footer_entry_meta_items', function( $items ) {
    if ( is_single() && in_category( 'portafolio' ) ) {
        return array();
    }
    return $items;
} );

// 3. Eliminar navegación Siguiente / Anterior SOLO en el portafolio
add_filter( 'generate_show_post_navigation', function( $show ) {
    if ( is_single() && in_category( 'portafolio' ) ) {
        return false;
    }
    return $show;
} );

// Cambiar el texto del Copyright en el pie de página de GeneratePress
add_filter( 'generate_copyright', function() {
    return '&copy; ' . date('Y') . ' Ricardo Leal Piñeres. Todos los derechos reservados.';
} );

// Script para el Toggle de Modo Oscuro/Claro
add_action('wp_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('theme-toggle');
            const body = document.body;

            // Restaurar tema guardado en localStorage
            if (localStorage.getItem('theme') === 'light') {
                body.classList.add('light-mode');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    body.classList.toggle('light-mode');
                    const isLight = body.classList.contains('light-mode');
                    localStorage.setItem('theme', isLight ? 'light' : 'dark');
                });
            }
        });
    </script>
    <?php
});