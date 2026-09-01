<?php
/**
 * Ricardo Leal Portfolio functions and definitions
 */

// 1. Cargar estilos del tema hijo con invalidación de caché automática
add_action( 'wp_enqueue_scripts', 'ricardo_leal_enqueue_styles', 10 );
function ricardo_leal_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    
    wp_enqueue_style( 
        'child-style', 
        get_stylesheet_uri(), 
        array( 'parent-style' ), 
        filemtime( get_stylesheet_directory() . '/style.css' ) 
    );
}

// 2. Shortcode: [github_stars repo="usuario/repositorio"]
add_shortcode( 'github_stars', function( $atts ) {
    $atts = shortcode_atts( array(
        'repo' => '',
    ), $atts );

    if ( empty( $atts['repo'] ) ) {
        return '';
    }

    $repo_clean    = sanitize_text_field( $atts['repo'] );
    $transient_key = 'gh_stars_' . md5( $repo_clean );
    $stars         = get_transient( $transient_key );

    if ( false === $stars ) {
        $response = wp_remote_get( 'https://api.github.com/repos/' . $repo_clean, array(
            'headers' => array( 'User-Agent' => 'WordPress-Portfolio' ),
            'timeout' => 5,
        ) );

        // Validar que no hay error de conexión Y que el código de respuesta HTTP sea 200 OK
        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return '';
        }

        $body  = json_decode( wp_remote_retrieve_body( $response ), true );
        $stars = isset( $body['stargazers_count'] ) ? intval( $body['stargazers_count'] ) : 0;

        // Guardar en caché por 12 horas
        set_transient( $transient_key, $stars, 12 * HOUR_IN_SECONDS );
    }

    if ( $stars <= 0 ) {
        return '';
    }

    return esc_html( $stars ) . ' ★';
} );

// 3. Cambiar texto del Copyright en el pie de página
add_filter( 'generate_copyright', function() {
    return '&copy; ' . date('Y') . ' Ricardo Leal Piñeres. Todos los derechos reservados.';
} );

// 4. Modo Oscuro/Claro: Aplicar clase en <head> para evitar parpadeo (FOUC)
add_action( 'wp_head', function() {
    ?>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <?php
}, 1 );

// 5. Listener para el botón de Toggle del Modo Oscuro/Claro
add_action( 'wp_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('theme-toggle');
            const root = document.documentElement;

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    root.classList.toggle('light-mode');
                    const isLight = root.classList.contains('light-mode');
                    localStorage.setItem('theme', isLight ? 'light' : 'dark');
                });
            }
        });
    </script>
    <?php
} );