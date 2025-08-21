document.addEventListener("DOMContentLoaded", function () {
  //Confirmación antes de eliminar
  const deleteButtons = document.querySelectorAll(".btn-delete");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm("¿Está seguro que desea eliminar este registro?")) {
        e.preventDefault();
      }
    });
  });

  // Funcionalidades
  // Validación de contraseña en tiempo real
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm_password");

  if (password && confirmPassword) {
    function validatePassword() {
      if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity("Las contraseñas no coinciden");
      } else {
        confirmPassword.setCustomValidity("");
      }
    }

    password.onchange = validatePassword;
    confirmPassword.onkeyup = validatePassword;
  }
});