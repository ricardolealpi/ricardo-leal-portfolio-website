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