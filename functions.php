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

// 1. El shortcode ahora acepta un post_id explícito (con fallback a get_the_ID() por si se usa fuera de un query loop, p. ej. en una entrada normal)
add_shortcode( 'github_stars', function ( $atts ) {
	$atts    = shortcode_atts( array( 'post_id' => 0 ), $atts, 'github_stars' );
	$post_id = $atts['post_id'] ? intval( $atts['post_id'] ) : get_the_ID();
 
	if ( ! $post_id ) {
		return '<!-- GH Debug: No post_id found -->';
	}
 
	$github_url = get_post_meta( $post_id, 'github_url', true );
	if ( empty( $github_url ) ) {
		return '<!-- GH Debug: Meta github_url vacia en ID ' . $post_id . ' -->';
	}
 
	$path  = trim( parse_url( $github_url, PHP_URL_PATH ), '/' );
	$parts = explode( '/', $path );
	if ( count( $parts ) < 2 ) {
		return '<!-- GH Debug: URL invalida en ID ' . $post_id . ' -->';
	}
 
	$repo          = sanitize_text_field( $parts[0] . '/' . $parts[1] );
	$transient_key = 'gh_stars_v4_' . md5( $repo );
	$stars         = get_transient( $transient_key );
 
	if ( false === $stars ) {
		$response = wp_remote_get( 'https://api.github.com/repos/' . $repo, array(
			'headers' => array( 'User-Agent' => 'WordPress-Portfolio-App' ),
			'timeout' => 5,
		) );
 
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return '';
		}
 
		$body  = json_decode( wp_remote_retrieve_body( $response ), true );
		$stars = isset( $body['stargazers_count'] ) ? intval( $body['stargazers_count'] ) : 0;
		set_transient( $transient_key, $stars, 12 * HOUR_IN_SECONDS );
	}
 
	if ( $stars <= 0 ) {
		return '';
	}
 
return '<span class="card-stars"><span>' . esc_html( $stars ) . '</span> <span class="star-icon">★</span></span>';
} );
 
// 2. LA CLAVE DEL FIX: interceptar el bloque nativo "Shortcode" justo cuando GenerateBlocks todavía está dentro de la iteración del Query Loop, cuando get_the_ID() sí apunta a la entrada correcta (240, 242...). Inyectamos ahí el post_id real como atributo y forzamos do_shortcode() de inmediato, en vez de dejar que WordPress lo haga luego.

add_filter( 'render_block_core/shortcode', function ( $block_content, $block ) {
	if ( strpos( $block_content, '[github_stars' ) === false ) {
		return $block_content;
	}
 
	$post_id       = get_the_ID();
	$block_content = str_replace(
		'[github_stars]',
		'[github_stars post_id="' . $post_id . '"]',
		$block_content
	);
 
	return do_shortcode( $block_content );
}, 10, 2 );
 
// 3. Borrar la caché de estrellas al actualizar la entrada
add_action( 'save_post', function ( $post_id ) {
	$github_url = get_post_meta( $post_id, 'github_url', true );
	if ( $github_url ) {
		$path  = trim( parse_url( $github_url, PHP_URL_PATH ), '/' );
		$parts = explode( '/', $path );
		if ( count( $parts ) >= 2 ) {
			$repo = sanitize_text_field( $parts[0] . '/' . $parts[1] );
			delete_transient( 'gh_stars_v4_' . md5( $repo ) ); // antes: 'gh_stars_V2_' (no coincidía)
		}
	}
} );

// 4. Cambiar texto del Copyright en el pie de página
add_filter( 'generate_copyright', function() {
    return '&copy; ' . date('Y') . ' Ricardo Leal Piñeres. Todos los derechos reservados.';
} );

// 5. Modo Oscuro/Claro: Aplicar clase en <head> para evitar parpadeo (FOUC)
add_action( 'wp_head', function() {
    ?>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <?php
}, 1 );

// 6. Listener para el botón de Toggle del Modo Oscuro/Claro
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