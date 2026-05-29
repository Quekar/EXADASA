const handleOpenSidebar = (e) => {
  const sidebar = document.getElementById("sidebar");
  const wraper = document.querySelector(".wrapper");
  const div = document.createElement("div");
  div.id = "overlay";
  div.style.position = "fixed";
  div.style.inset = "0";
  div.style.zIndex = "20";
  div.style.background = "rgba(0, 0, 0, 0.5)";
  if (sidebar.classList.contains("active-aside")) {
    sidebar.classList.remove("active-aside");
    wraper.removeChild(div);
  } else {
    sidebar.classList.add("active-aside");
    wraper.appendChild(div);
  }
};

window.addEventListener("click", (e) => {
  const sidebar = document.getElementById("sidebar");
  const btnSidebar = document.getElementById("btn-sidebar");
  const overlay = document.getElementById("overlay");
  if (btnSidebar) {
    const trigerBtn = btnSidebar.querySelector("i");
    if (
      e.target !== sidebar &&
      !sidebar.contains(e.target) &&
      trigerBtn !== e.target &&
      overlay
    ) {
      sidebar.classList.remove("active-aside");
      overlay.remove();
    }
  }
});

const isPathname = ["/exadasa/login", "/exadasa/", "/exadasa/maintenance"];

if (!isPathname.includes(window.location.pathname)) {
  let timeout = null;
  let touch = false;
  const batasWaktu = 30 * 60 * 1000;

  function showToastAsync() {
    return new Promise((resolve) => {
      const elevation = document.createElement("div");
      elevation.style.position = "fixed";
      elevation.style.inset = "0";
      elevation.style.zIndex = "99";
      elevation.style.backgroundColor = "rgba(0, 0, 0, 0.5)";

      document.body.appendChild(elevation);

      const toast = document.createElement("div");
      toast.style.position = "fixed";
      toast.style.top = "50%";
      toast.style.left = "50%";
      toast.style.background = "#eef2f6";
      toast.style.color = "white";
      toast.style.borderRadius = "14px";
      toast.style.padding = "16px 20px";
      toast.style.boxShadow = "1px 1px 15px -2px rgba(0,0,0,0.2)";
      toast.style.display = "flex";
      toast.style.flexDirection = "column";
      toast.style.border = "1px solid rgba(255,255,255,0.3)";
      toast.style.gap = "6px";
      toast.style.zIndex = "100";
      toast.style.transform = "translate(-50%, -50%)";
      toast.style.transition = "all 0.5s ease";

      toast.innerHTML = `
    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 6px;">
        <span style="font-size: 28px;">
            <i class='ph ph-info' style="color: #3b82f6;"></i>
        </span>

        <span style="font-weight:400; font-size:15px; color: #000">
            Sesi anda telah berakhir, silahkan login kembali
        </span>
    </div>
  `;

      elevation.appendChild(toast);

      const groupButton = document.createElement("div");
      groupButton.style.display = "flex";
      groupButton.style.alignItems = "center";
      groupButton.style.justifyContent = "flex-end";
      groupButton.style.gap = "10px";

      toast.appendChild(groupButton);

      const button = document.createElement("button");
      button.classList.add("poppins-regular");
      button.style.backgroundColor = "#ef4444";
      button.style.color = "white";
      button.style.border = "none";
      button.style.padding = "5px 10px";
      button.style.borderRadius = "8px";
      button.style.cursor = "pointer";
      button.style.width = "fit-content";
      button.textContent = "Tutup";
      button.addEventListener("click", async () => {
        await fetch("/exadasa/dashboard/logoutSession");
        elevation.remove();
        window.location.href = "/exadasa/login";
        resolve(elevation);
      });

      const button2 = document.createElement("button");
      button2.classList.add("poppins-regular");
      button2.style.backgroundColor = "#3b82f6";
      button2.style.color = "white";
      button2.style.border = "none";
      button2.style.padding = "5px 10px";
      button2.style.borderRadius = "8px";
      button2.style.cursor = "pointer";
      button2.style.width = "fit-content";
      button2.textContent = "Extends";
      button2.addEventListener("click", async () => {
        elevation.remove();
        resolve(elevation);
      });

      groupButton.appendChild(button);
      groupButton.appendChild(button2);

      document.body.appendChild(elevation);
    });
  }

  function resetTimer() {
    if (touch) return;
    clearTimeout(timeout);
    touch = true;

    timeout = setTimeout(async () => {
      const toast = await showToastAsync();
      touch = false;
    }, batasWaktu);
  }

  ["mousemove", "keydown", "click", "scroll", "touchstart"].forEach((event) => {
    document.addEventListener(event, resetTimer);
  });

  resetTimer();
}
