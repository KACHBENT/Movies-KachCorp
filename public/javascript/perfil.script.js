(() => {
  "use strict";

  const input = document.getElementById("foto");
  const preview = document.getElementById("avatarPreview");

  if (!input || !preview) return;

  const MAX_MB = 5;
  const ALLOWED = ["image/jpeg", "image/jpg", "image/png", "image/webp"];

  input.addEventListener("change", () => {
    const file = input.files && input.files[0] ? input.files[0] : null;
    if (!file) return;

    if (!ALLOWED.includes(file.type)) {
      alert("Formato inválido. Usa JPG, PNG o WEBP.");
      input.value = "";
      return;
    }

    const sizeMB = file.size / (1024 * 1024);
    if (sizeMB > MAX_MB) {
      alert("La imagen excede 5 MB.");
      input.value = "";
      return;
    }

    const url = URL.createObjectURL(file);
    preview.src = url;

    // liberar memoria
    preview.onload = () => URL.revokeObjectURL(url);
  });
})();