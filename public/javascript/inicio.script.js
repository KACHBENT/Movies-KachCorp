(function () {
  "use strict";

  const modalEl = document.getElementById("videoModal");
  const frameWrap = document.getElementById("videoFrameWrap");
  const frame = document.getElementById("videoFrame");

  const videoWrap = document.getElementById("videoTagWrap");
  const video = document.getElementById("videoTag");
  const source = document.getElementById("videoSource");

  const heroCarousel = document.getElementById("heroCarousel");

  function isMp4(url) {
    return typeof url === "string" && url.toLowerCase().includes(".mp4");
  }

  function stopAll() {

    frame.src = "";
    frameWrap.classList.add("d-none");


    video.pause();
    source.src = "";
    video.load();
    videoWrap.classList.add("d-none");
  }

  function openVideo(url) {
    stopAll();

    if (isMp4(url)) {
      source.src = url;
      videoWrap.classList.remove("d-none");
      video.load();
      video.play().catch(() => {});
      return;
    }

    const hasQuery = url.includes("?");
    const autoplayUrl = url + (hasQuery ? "&" : "?") + "autoplay=1&rel=0";
    frame.src = autoplayUrl;
    frameWrap.classList.remove("d-none");
  }


  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-video]");
    if (!btn) return;

    const url = btn.getAttribute("data-video");
    if (!url) return;

    openVideo(url);
  });

  if (modalEl) {
    modalEl.addEventListener("hidden.bs.modal", stopAll);
  }

  if (heroCarousel) {
    heroCarousel.addEventListener("slide.bs.carousel", () => {
      // si el modal está visible
      if (modalEl && modalEl.classList.contains("show")) {
        stopAll();
      }
    });
  }
})();
