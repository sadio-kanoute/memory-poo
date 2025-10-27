// Affiche un aperçu des images dans la page d'accueil
(function(){
  const images = [
    'img/gallery/1.jpg','img/gallery/2.jpg','img/gallery/3.jpg','img/gallery/4.jpg','img/gallery/5.jpg','img/gallery/6.jpg','img/gallery/7.jpg','img/gallery/8.jpg','img/gallery/9.jpg','img/gallery/10.svg','img/gallery/11.jpg','img/gallery/12.jpg','img/gallery/13.jpg','img/gallery/14.jpg','img/gallery/15.jpg','img/gallery/16.jpg','img/gallery/17.jpg','img/gallery/18.jpg'
  ];
  const preview = document.getElementById('preview');
  if(!preview) return;
  images.slice(0,8).forEach(src=>{
    const f = document.createElement('figure'); f.className='card';
    const img = document.createElement('img'); img.src=src; img.alt='';
    f.appendChild(img); preview.appendChild(f);
  });
})();
