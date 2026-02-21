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
(function () {
  "use strict";

  const modalEl = document.getElementById("movieModal");

  const mTitle  = document.getElementById("mTitle");
  const mDesc   = document.getElementById("mDesc");
  const mPoster = document.getElementById("mPoster");

  const mGenero = document.getElementById("mGenero");
  const mClas   = document.getElementById("mClas");
  const mFecha  = document.getElementById("mFecha");

  const openLink = document.getElementById("mOpenLink");

  const frameWrap = document.getElementById("videoFrameWrap");
  const frame = document.getElementById("videoFrame");

  const videoWrap = document.getElementById("videoTagWrap");
  const video = document.getElementById("videoTag");
  const source = document.getElementById("videoSource");

  const fallback = document.getElementById("videoFallback");
  const fallbackLink = document.getElementById("videoFallbackLink");

  function isMp4(url) {
    return typeof url === "string" && url.toLowerCase().includes(".mp4");
  }
  function isMega(url) {
    return typeof url === "string" && /(^https?:\/\/)?(www\.)?mega\.nz\//i.test(url);
  }
  function megaToEmbed(url) {
    try {
      const u = new URL(url);
      if (!u.hostname.includes("mega.nz")) return url;
      const parts = u.pathname.split("/").filter(Boolean);
      if (parts.length >= 2 && parts[0] === "file") u.pathname = "/embed/" + parts[1];
      return u.toString();
    } catch { return url; }
  }

  function isYouTube(url){
    return typeof url === "string" && /(youtube\.com|youtu\.be)/i.test(url);
  }
  function ytToEmbed(url){
    try{
      const u = new URL(url);
      if (/youtu\.be/i.test(u.hostname)){
        const id = u.pathname.replace("/","");
        return "https://www.youtube.com/embed/" + id;
      }
      if (/youtube\.com/i.test(u.hostname)){
        const id = u.searchParams.get("v");
        if (id) return "https://www.youtube.com/embed/" + id;
      }
      return url;
    }catch{ return url; }
  }

  function isDailymotion(url){
    return typeof url === "string" && /dailymotion\.com/i.test(url);
  }
  function dmToEmbed(url){
    // si ya es embed (como geo.dailymotion player) lo dejamos
    if (/player/i.test(url)) return url;
    const m = url.match(/video\/([a-zA-Z0-9]+)/);
    if (m && m[1]) return "https://www.dailymotion.com/embed/video/" + m[1];
    return url;
  }

  function resetUI() {
    if (frame) frame.src = "";
    frameWrap.classList.add("d-none");

    if (video) video.pause();
    source.src = "";
    video.load();
    videoWrap.classList.add("d-none");

    fallback.classList.add("d-none");
    fallbackLink.href = "#";
    openLink.href = "#";
  }

  function openVideo(url) {
    resetUI();
    if (!url) {
      fallback.classList.remove("d-none");
      fallback.querySelector(".fw-semibold").textContent = "Esta película no tiene tráiler.";
      fallbackLink.classList.add("d-none");
      openLink.classList.add("d-none");
      return;
    }

    openLink.classList.remove("d-none");
    openLink.href = url;

    fallbackLink.classList.remove("d-none");
    fallbackLink.href = url;

    if (isMp4(url)) {
      source.src = url;
      videoWrap.classList.remove("d-none");
      video.load();
      video.play().catch(() => {});
      return;
    }

    if (isMega(url)) {
      const embedUrl = megaToEmbed(url);
      frameWrap.classList.remove("d-none");
      frame.src = embedUrl;

      const t = setTimeout(() => {
        frameWrap.classList.add("d-none");
        fallback.classList.remove("d-none");
      }, 2200);

      frame.onload = () => clearTimeout(t);
      return;
    }

    // YouTube
    if (isYouTube(url)) url = ytToEmbed(url);

    // Dailymotion
    if (isDailymotion(url)) url = dmToEmbed(url);

    frameWrap.classList.remove("d-none");
    frame.src = url;
  }

  if (modalEl) {
    modalEl.addEventListener("show.bs.modal", (ev) => {
      const btn = ev.relatedTarget;
      const title  = btn?.getAttribute("data-title")  || "Película";
      const desc   = btn?.getAttribute("data-desc")   || "";
      const poster = btn?.getAttribute("data-poster") || "<?= base_url('images/banners/banner1.png') ?>";
      const video  = btn?.getAttribute("data-video")  || "";

      const genero = btn?.getAttribute("data-genero") || "";
      const clas   = btn?.getAttribute("data-clas")   || "";
      const fecha  = btn?.getAttribute("data-fecha")  || "";

      mTitle.textContent = title;
      mDesc.textContent  = desc;
      mPoster.src        = poster;

      mGenero.textContent = genero ? ("• " + genero + " ") : "";
      mClas.textContent   = clas   ? ("• " + clas + " ") : "";
      mFecha.textContent  = fecha  ? ("• " + fecha) : "";

      openVideo(video);
    });

    modalEl.addEventListener("hidden.bs.modal", resetUI);
  }
})();