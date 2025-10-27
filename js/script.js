// Script: injection de la galerie à partir du dossier img/gallery
(function(){
  const images = [
    "img/gallery/1.jpg",
    "img/gallery/2.jpg",
    "img/gallery/3.jpg",
    "img/gallery/4.jpg",
    "img/gallery/5.jpg",
    "img/gallery/6.jpg",
    "img/gallery/7.jpg",
    "img/gallery/8.jpg",
    "img/gallery/9.jpg",
    "img/gallery/10.svg",
    "img/gallery/11.jpg",
    "img/gallery/12.jpg",
    "img/gallery/13.jpg",
    "img/gallery/14.jpg",
    "img/gallery/15.jpg",
    "img/gallery/16.jpg",
    "img/gallery/17.jpg",
    "img/gallery/18.jpg"
  ];

  const gallery = document.getElementById('gallery');
  const lightbox = document.getElementById('lightbox');
  const lbImg = lightbox.querySelector('img');
  const lbCaption = lightbox.querySelector('.lb-caption');
  const lbClose = lightbox.querySelector('.lb-close');
  const lbPrev = lightbox.querySelector('.lb-prev');
  const lbNext = lightbox.querySelector('.lb-next');

  let currentIndex = 0;

  function filenameToCaption(path){
    const parts = path.split('/').pop().split('.')[0];
    // Replace dashes/underscores and numbers to make a friendly caption
    return parts.replace(/[-_]/g,' ').replace(/\d+/g, match => `#${match}`) || 'Moment';
  }

  images.forEach((src, i) => {
    const el = document.createElement('figure');
    el.className = 'card';
    el.tabIndex = 0;
    const img = document.createElement('img');
    img.src = src;
    img.alt = filenameToCaption(src);
    const cap = document.createElement('figcaption');
    cap.className = 'caption';
    cap.textContent = filenameToCaption(src);
    el.appendChild(img);
    el.appendChild(cap);
    el.addEventListener('click', ()=> openAt(i));
    el.addEventListener('keydown', (e)=>{ if(e.key==='Enter') openAt(i)});
    gallery.appendChild(el);
  });

  function openAt(index){
    currentIndex = index;
    lbImg.src = images[currentIndex];
    lbImg.alt = filenameToCaption(images[currentIndex]);
    lbCaption.textContent = filenameToCaption(images[currentIndex]);
    lightbox.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
  }

  function closeLB(){
    lightbox.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
  }

  function prev(){ currentIndex = (currentIndex - 1 + images.length) % images.length; openAt(currentIndex); }
  function next(){ currentIndex = (currentIndex + 1) % images.length; openAt(currentIndex); }

  lbClose.addEventListener('click', closeLB);
  lbPrev.addEventListener('click', (e)=>{ e.stopPropagation(); prev(); });
  lbNext.addEventListener('click', (e)=>{ e.stopPropagation(); next(); });

  // keyboard
  document.addEventListener('keydown', (e)=>{
    if(lightbox.getAttribute('aria-hidden')==='false'){
      if(e.key==='ArrowLeft') prev();
      if(e.key==='ArrowRight') next();
      if(e.key==='Escape') closeLB();
    }
  });

  // click outside to close
  lightbox.addEventListener('click', (e)=>{
    if(e.target === lightbox) closeLB();
  });

})();
