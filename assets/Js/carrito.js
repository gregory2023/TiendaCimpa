document.addEventListener('DOMContentLoaded', () => {
    const botonesAgregar = document.querySelectorAll('.agregar-carrito, .agregar-carrito-mascota');

    // Intenta cargar SweetAlert2 dinámicamente si no está ya presente en la página
    // Esto es útil si la página de productos no siempre carga SweetAlert2 por sí misma.
    if (typeof Swal === 'undefined') {
        const scriptSwal = document.createElement('script');
        scriptSwal.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        scriptSwal.onload = () => console.log("SweetAlert2 cargado dinámicamente.");
        scriptSwal.onerror = () => console.error("Error al cargar SweetAlert2 dinámicamente.");
        document.head.appendChild(scriptSwal);
    }

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', (event) => {
            const botonClicado = event.currentTarget; // Usar currentTarget es más seguro aquí
            const card = botonClicado.closest('.card.producto-card');

            if (!card) {
                console.error("Error: No se pudo encontrar el elemento '.card.producto-card' ancestro del botón.");
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'No se pudo obtener la información del producto (tarjeta no encontrada).', 'error');
                } else {
                    alert('No se pudo obtener la información del producto (tarjeta no encontrada).');
                }
                return;
            }

            const id = botonClicado.dataset.id;
            const nombreElement = card.querySelector('.producto-titulo');
            const precioCompletoElement = card.querySelector('.producto-precio');
            const imagenElement = card.querySelector('.producto-imagen');
            // const stockElement = card.querySelector('.producto-stock'); // Para futura lógica de stock

            if (!id || !nombreElement || !precioCompletoElement || !imagenElement) {
                console.error("Error: Faltan elementos de datos del producto en la tarjeta.", {
                    id,
                    nombre: nombreElement ? 'OK' : 'Falta',
                    precio: precioCompletoElement ? 'OK' : 'Falta',
                    imagen: imagenElement ? 'OK' : 'Falta'
                });
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'No se pudieron obtener todos los detalles del producto.', 'error');
                } else {
                    alert('No se pudieron obtener todos los detalles del producto.');
                }
                return;
            }

            const nombre = nombreElement.innerText.trim();
            const imagenSrc = imagenElement.src;
            const precioTexto = precioCompletoElement.innerText; // Ej: "Precio: 50.00 CinpaCoins"

            // Extraer el valor numérico del precio
            // Esta expresión regular busca un número (puede tener decimales con punto o coma)
            const matchPrecio = precioTexto.match(/(\d+([.,]\d+)?)/);
            let precioNumero;

            if (matchPrecio && matchPrecio[1]) {
                // Reemplaza la coma por punto para asegurar que parseFloat funcione correctamente
                precioNumero = parseFloat(matchPrecio[1].replace(',', '.'));
            } else {
                console.error(`Error: No se pudo extraer el precio numérico del texto: "${precioTexto}"`);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error de Precio', `No se pudo determinar el precio del producto: ${nombre}.`, 'error');
                } else {
                    alert(`No se pudo determinar el precio del producto: ${nombre}.`);
                }
                return;
            }

            if (isNaN(precioNumero)) {
                console.error(`Error: El precio extraído no es un número válido: "${precioNumero}" (original: "${precioTexto}")`);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error de Precio', `El precio del producto ${nombre} no es válido.`, 'error');
                } else {
                    alert(`El precio del producto ${nombre} no es válido.`);
                }
                return;
            }

            const productoAAgregar = {
                id: id,
                nombre: nombre,
                precio: precioNumero,
                imagen: imagenSrc,
                cantidad: 1 // Siempre se añade 1 al hacer clic aquí
            };

            // Llama a la función que maneja la lógica del carrito en localStorage
            gestionarProductoEnCarrito(productoAAgregar);
        });
    });
});

/**
 * Gestiona la adición de un producto al carrito en localStorage.
 * Si el producto ya existe, incrementa su cantidad.
 * Si no existe, lo añade al carrito.
 * @param {object} producto - El objeto del producto a añadir/actualizar.
 */
function gestionarProductoEnCarrito(producto) {
    // Obtener el carrito actual de localStorage, o un array vacío si no existe
    let carrito = JSON.parse(localStorage.getItem('carritoCIMPA')) || [];

    const indiceProductoExistente = carrito.findIndex(item => item.id === producto.id);

    if (indiceProductoExistente > -1) {
        // El producto ya está en el carrito, incrementa la cantidad
        // Aquí podrías añadir una comprobación contra el stock si lo tuvieras disponible
        carrito[indiceProductoExistente].cantidad += 1;
    } else {
        // El producto es nuevo, añádelo al carrito
        carrito.push(producto);
    }

    // Guarda el carrito actualizado en localStorage
    localStorage.setItem('carritoCIMPA', JSON.stringify(carrito));

    // Muestra una notificación al usuario
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: '¡Producto Añadido!',
            html: `<b>${producto.nombre}</b> se ha añadido a tu carrito.`,
            toast: true, // Lo hace más pequeño y discreto
            position: 'top-end', // Aparece en la esquina superior derecha
            showConfirmButton: false, // No necesita botón de confirmación
            timer: 2500, // Se cierra automáticamente después de 2.5 segundos
            timerProgressBar: true, // Muestra una barra de progreso
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    } else {
        // Fallback si SweetAlert2 no está disponible
        alert(`${producto.nombre} ha sido añadido al carrito.`);
    }

    // Opcional: Actualizar un contador de ítems en el carrito visible en la página
    actualizarContadorVisualCarrito();
}

/**
 * (Opcional) Actualiza un contador visual de ítems en el carrito en la página.
 * Necesitarías un elemento HTML con un ID específico (ej: 'cart-item-count') en tu header.
 */
function actualizarContadorVisualCarrito() {
    const carrito = JSON.parse(localStorage.getItem('carritoCIMPA')) || [];
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);

    // Busca un elemento en tu HTML para mostrar el contador (ej. en el header)
    // const contadorElemento = document.getElementById('contador-carrito-items');
    // if (contadorElemento) {
    //     contadorElemento.innerText = totalItems;
    //     contadorElemento.style.display = totalItems > 0 ? 'inline-block' : 'none'; // Muestra/oculta si hay items
    // }
    console.log(`Total de ítems en el carrito: ${totalItems}`);
    console.log("Estado actual del carrito en localStorage:", carrito);
}

// Llama a la actualización del contador al cargar la página, por si ya hay items de una sesión anterior.
document.addEventListener('DOMContentLoaded', actualizarContadorVisualCarrito);