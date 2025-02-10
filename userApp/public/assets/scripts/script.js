// Espera a que el DOM esté completamente cargado antes de ejecutar el código
document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todos los enlaces que tengan la clase 'borrar'
    const deleteLinks = document.querySelectorAll('.borrar');
    
    // Por cada enlace de borrado encontrado, añade un event listener
    deleteLinks.forEach(function(link) {

        link.addEventListener('click', function(e) {
            // Previene el comportamiento por defecto del enlace
            e.preventDefault();
            
            // Muestra un diálogo de confirmación
            if (confirm('Are you sure?')) {
                // Obtiene el ID del usuario del atributo data-href
                const userId = this.getAttribute('data-href').split('/').pop();
                
                // Busca el formulario de borrado correspondiente usando el ID del usuario
                const deleteForm = document.getElementById('delete-form-' + userId);
                
                // Si encuentra el formulario, lo envía
                if (deleteForm) {
                    deleteForm.submit();
                } else {
                    // Si no encuentra el formulario, muestra un error en la consola
                    console.error('Delete form not found for user ID:', userId);
                }
            }
        });
    });
});