const toggle=document.querySelector('.menu-toggle');
toggle?.addEventListener('click',()=>{const open=document.body.classList.toggle('menu-open');toggle.setAttribute('aria-expanded',String(open));toggle.textContent=open?'Close ×':'Menu +';});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.body.classList.remove('menu-open');toggle?.setAttribute('aria-expanded','false');if(toggle)toggle.textContent='Menu +';document.querySelectorAll('details[open]').forEach(d=>d.removeAttribute('open'));}});
const filter=document.querySelector('[data-filter]');
filter?.addEventListener('input',()=>{const q=filter.value.toLowerCase().trim();let count=0;document.querySelectorAll('[data-search]').forEach(card=>{card.hidden=!card.dataset.search.includes(q);if(!card.hidden)count++;});document.querySelector('.results-count').textContent=`${count} articles`;document.querySelector('.empty').style.display=count?'none':'block';});
