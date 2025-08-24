<?php
/**
 * Funciones para manejar la configuración del sitio
 */

/**
 * Obtener la configuración del sitio desde la base de datos
 */
function obtenerConfiguracionSitio($conexion) {
    try {
        $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
        $stmt->execute();
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Valores por defecto si no hay configuración
        if (!$config) {
            return [
                'nombre_sitio' => 'UTH SOLUTIONS REAL STATE',
                'color_esquema' => 'azul',
                'mensaje_banner' => 'Encuentra la casa de tus sueños',
                'titulo_quienes_somos' => 'Quienes Somos',
                'descripcion_quienes_somos' => 'Somos una empresa dedicada a ayudarte a encontrar la propiedad perfecta.',
                'facebook_url' => '',
                'youtube_url' => '',
                'instagram_url' => '',
                'direccion' => '',
                'telefono_contacto' => '',
                'email_contacto' => '',
                'logo_navbar' => null,
                'logo_footer' => null,
                'banner_imagen' => null
            ];
        }
        
        return $config;
    } catch (PDOException $e) {
        // En caso de error, devolver configuración por defecto
        return [
            'nombre_sitio' => 'UTH SOLUTIONS REAL STATE',
            'color_esquema' => 'azul',
            'mensaje_banner' => 'Encuentra la casa de tus sueños',
            'titulo_quienes_somos' => 'Quienes Somos',
            'descripcion_quienes_somos' => 'Somos una empresa dedicada a ayudarte a encontrar la propiedad perfecta.',
            'facebook_url' => '',
            'youtube_url' => '',
            'instagram_url' => '',
            'direccion' => '',
            'telefono_contacto' => '',
            'email_contacto' => '',
            'logo_navbar' => null,
            'logo_footer' => null,
            'banner_imagen' => null
        ];
    }
}

/**
 * Generar el CSS dinámico para el esquema de colores
 */
function generarCSSEsquema($config) {
    $esquema = $config ? $config['color_esquema'] : 'azul';
    
    $css = ":root {\n";
    
    switch ($esquema) {
        case 'amarillo':
            $css .= "    --color-primario: #ffd700;\n";
            $css .= "    --color-secundario: #333333;\n";
            $css .= "    --color-hover: #ffed4e;\n";
            break;
        case 'gris':
            $css .= "    --color-primario: #6b7280;\n";
            $css .= "    --color-secundario: #374151;\n";
            $css .= "    --color-hover: #9ca3af;\n";
            break;
        case 'blanco_gris':
            $css .= "    --color-primario: #ffffff;\n";
            $css .= "    --color-secundario: #f5f5f5;\n";
            $css .= "    --color-hover: #e5e5e5;\n";
            break;
        case 'azul':
        default:
            $css .= "    --color-primario: #1a237e;\n";
            $css .= "    --color-secundario: #303f9f;\n";
            $css .= "    --color-hover: #3f51b5;\n";
            break;
    }
    
    $css .= "}\n\n";
    $css .= "
    .navbar {
        background-color: var(--color-primario) !important;
    }
    
    .btn-primary {
        background-color: var(--color-primario) !important;
        border-color: var(--color-primario) !important;
    }
    
    .btn-primary:hover {
        background-color: var(--color-hover) !important;
        border-color: var(--color-hover) !important;
    }
    
    .section-title {
        color: var(--color-primario) !important;
    }
    
    .footer {
        background-color: var(--color-primario) !important;
    }
    
    .property-card:hover {
        border-color: var(--color-primario) !important;
    }
    
    .price {
        color: var(--color-primario) !important;
    }
    ";
    
    return $css;
}

/**
 * Obtener la URL del logo del navbar
 */
function obtenerLogoNavbar($config) {
    if ($config && $config['logo_navbar'] && file_exists('uploads/logos/' . $config['logo_navbar'])) {
        return 'uploads/logos/' . $config['logo_navbar'];
    }
    return 'images/logo.png'; // Logo por defecto
}

/**
 * Obtener la URL del logo del footer
 */
function obtenerLogoFooter($config) {
    if ($config && $config['logo_footer'] && file_exists('uploads/logos/' . $config['logo_footer'])) {
        return 'uploads/logos/' . $config['logo_footer'];
    }
    return 'images/logo.png'; // Logo por defecto
}

/**
 * Obtener la URL de la imagen del banner
 */
function obtenerImagenBanner($config) {
    if ($config && $config['banner_imagen'] && file_exists('uploads/banners/' . $config['banner_imagen'])) {
        return 'uploads/banners/' . $config['banner_imagen'];
    }
    return null; // No hay imagen de banner personalizada
}
?>
