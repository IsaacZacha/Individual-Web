/**
 * Laravel Echo Setup para Reverb WebSocket - Versión Ultra Simplificada
 */

console.log('🔄 Cargando script de Laravel Echo...');

// Configuración inicial
window.Echo = null;

// Función directa de inicialización
function initializeEcho() {
    console.log('� Iniciando Laravel Echo...');
    console.log('🔍 Estado de dependencias:', {
        Pusher: typeof Pusher,
        Echo: typeof Echo,
        windowEcho: typeof window.Echo,
        globalThis: typeof globalThis.Echo
    });
    
    // Verificar Pusher
    if (typeof Pusher === 'undefined') {
        console.error('❌ Pusher no está disponible - verificar que se haya cargado');
        return false;
    }
    
    // Verificar Echo - múltiples formas posibles
    let EchoConstructor = null;
    
    if (typeof Echo !== 'undefined') {
        EchoConstructor = Echo;
        console.log('✅ Usando Echo global');
    } else if (window.Echo && typeof window.Echo === 'function') {
        EchoConstructor = window.Echo;
        console.log('✅ Usando window.Echo');
    } else {
        console.error('❌ Laravel Echo no está disponible');
        console.log('🔍 Variables disponibles:', Object.keys(window).filter(k => k.toLowerCase().includes('echo')));
        return false;
    }
    
    try {
        // Crear instancia de Echo
        const config = {
            broadcaster: 'reverb',
            key: window.appConfig?.appKey || 'gzbmxzp7oozsmb8cor5t',
            wsHost: window.appConfig?.wsHost || '127.0.0.1',
            wsPort: window.appConfig?.wsPort || 8080,
            wssPort: window.appConfig?.wssPort || 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss']
        };
        
        console.log('⚙️ Configuración Echo:', config);
        
        // Intentar crear instancia
        window.Echo = new EchoConstructor(config);
        
        console.log('🎉 Laravel Echo inicializado exitosamente');
        console.log('📡 Instancia Echo:', window.Echo);
        
        return true;
        
    } catch (error) {
        console.error('❌ Error creando instancia de Echo:', error);
        return false;
    }
}

// Función de espera mejorada
function waitAndInit() {
    let attempts = 0;
    const maxAttempts = 15;
    
    function checkAndInit() {
        attempts++;
        console.log(`⏱️ Intento ${attempts}/${maxAttempts} - Verificando dependencias...`);
        
        // Verificar que ambas librerías estén disponibles
        if (typeof Pusher !== 'undefined' && typeof Echo !== 'undefined') {
            console.log('✅ Dependencias cargadas, inicializando...');
            
            if (initializeEcho()) {
                console.log('🚀 Echo configurado y listo');
                return; // Éxito, salir
            }
        }
        
        // Si no está listo y aún tenemos intentos
        if (attempts < maxAttempts) {
            console.log(`⏳ Esperando librerías... (${attempts}/${maxAttempts})`);
            setTimeout(checkAndInit, 300);
        } else {
            console.error('❌ Timeout: No se pudieron cargar las dependencias');
            console.log('🔍 Estado final:', {
                Pusher: typeof Pusher,
                Echo: typeof Echo,
                windowKeys: Object.keys(window).filter(k => k.toLowerCase().includes('echo') || k.toLowerCase().includes('pusher'))
            });
        }
    }
    
    // Iniciar verificación
    checkAndInit();
}

// Esperar a que el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOM cargado, iniciando configuración Echo...');
        setTimeout(waitAndInit, 100);
    });
} else {
    console.log('📄 DOM ya está listo, iniciando configuración Echo...');
    setTimeout(waitAndInit, 100);
}

// Exportar función para uso manual si es necesario
window.manualInitEcho = initializeEcho;
