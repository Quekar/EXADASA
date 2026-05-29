function showToast(type, message) {
  const colors = {
    success: "#22c55e",
    error: "#ef4444",
    warning: "#facc15",
    info: "#3b82f6",
  };

  const icons = {
    success: "<i class='ph ph-check'></i>",
    error: "<i class='ph ph-x'></i>",
    warning: "<i class='ph ph-exclamation'></i>",
    info: "<i class='ph ph-info'></i>",
  };

  const toast = document.createElement("div");

  toast.style.position = "fixed";
  toast.style.top = "20px";
  toast.style.right = "20px";
  toast.style.background = colors[type];
  toast.style.color = "white";
  toast.style.padding = "14px 20px";
  toast.style.borderRadius = "14px";
  toast.style.boxShadow = "1px 1px 15px rgba(0,0,0,0.2)";
  toast.style.display = "flex";
  toast.style.alignItems = "center";
  toast.style.gap = "10px";
  toast.style.zIndex = "9999";

  toast.style.transform = "translateX(150%)";
  toast.style.opacity = "0";
  toast.style.transition = "all 0.5s ease";

  toast.innerHTML = `
    <span style="font-size:16px;">
      ${icons[type]}
    </span>

    <span style="font-weight:500; font-size:15px;">
      ${message}
    </span>
  `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.transform = "translateX(0)";
    toast.style.opacity = "1";
  }, 10);

  setTimeout(() => {
    toast.style.transform = "translateX(150%)";
    toast.style.opacity = "0";

    setTimeout(() => {
      toast.remove();
    }, 500);
  }, 3000);
}
