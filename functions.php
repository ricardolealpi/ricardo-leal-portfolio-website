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

// Shortcode 100% automático: [github_stars]
add_shortcode( 'github_stars', function() {
    // 1. Obtener el ID de la entrada actual en el Query Loop
    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return '';
    }

    // 2. Leer la URL guardada en el campo personalizado 'github_url'
    $github_url = get_post_meta( $post_id, 'github_url', true );
    if ( empty( $github_url ) ) {
        return '';
    }

    // 3. Extraer automáticamente 'usuario/repositorio' de la URL (ej: ricardolealpi/windows-server-automation)
    $path  = trim( parse_url( $github_url, PHP_URL_PATH ), '/' );
    $parts = explode( '/', $path );

    if ( count( $parts ) < 2 ) {
        return '';
    }

    $repo          = sanitize_text_field( $parts[0] . '/' . $parts[1] );
    $transient_key = 'gh_stars_' . md5( $repo );
    $stars         = get_transient( $transient_key );

    // 4. Consultar API si no hay caché guardada
    if ( false === $stars ) {
        $response = wp_remote_get( 'https://api.github.com/repos/' . $repo, array(
            'headers' => array( 'User-Agent' => 'WordPress-Portfolio' ),
            'timeout' => 5,
        ) );

        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return '';
        }

        $body  = json_decode( wp_remote_retrieve_body( $response ), true );
        $stars = isset( $body['stargazers_count'] ) ? intval( $body['stargazers_count'] ) : 0;

        set_transient( $transient_key, $stars, 12 * HOUR_IN_SECONDS );
    }

    // 5. Si tiene 0 estrellas o no existe el repo, no renderiza nada
    if ( $stars <= 0 ) {
        return '';
    }

    return '<span class="card-stars"><span>' . esc_html( $stars ) . '</span> ★</span>';
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