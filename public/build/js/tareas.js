!function(){!async function(){try{const t=`api/tareas?id=${i()}`,a=await fetch(t),n=await a.json();e=n.tareas,o()}catch(e){console.log(e)}}();let e=[],t=[];document.querySelector("#agregar-tarea").addEventListener("click",(function(){r(!1)}));const a=document.querySelectorAll('#filtros input[type="radio"]');function n(a){const n=a.target.value;t=""!==n?e.filter((e=>e.estado===n)):[],o()}function o(){!function(){const e=document.querySelector("#listado-tareas");for(;e.firstChild;)e.removeChild(e.firstChild)}(),function(){let n;a.forEach((e=>{e.checked&&(n=e.value)})),t=""!==n?e.filter((e=>e.estado===n)):[]}(),function(){const t=e.filter((e=>"0"===e.estado)),a=document.querySelector("#pendientes");0===t.length?a.disabled=!0:a.disabled=!1}(),function(){const t=e.filter((e=>"1"===e.estado)),a=document.querySelector("#completadas");0===t.length?a.disabled=!0:a.disabled=!1}();const n=t.length?t:e;if(0===n.length){const e=document.querySelector("#listado-tareas"),t=document.createElement("LI");return t.textContent="No hay tareas",t.classList.add("no-tareas"),void e.appendChild(t)}const c={0:"Pendiente",1:"Completa"};n.forEach((t=>{const a=document.createElement("LI");a.dataset.tareaId=t.id,a.classList.add("tarea");const n=document.createElement("P");n.textContent=t.nombre,n.ondblclick=function(){r(editar=!0,{...t})};const s=document.createElement("DIV");s.classList.add("opciones");const l=document.createElement("BUTTON");l.classList.add("estado-tarea"),l.classList.add(`${c[t.estado].toLowerCase()}`),l.textContent=c[t.estado],l.dataset.estadoTarea=t.estado,l.ondblclick=function(){!function(e){const t="1"===e.estado?"0":"1";e.estado=t,d(e)}({...t})};const u=document.createElement("BUTTON");u.classList.add("eliminar-tarea"),u.dataset.idTarea=t.id,u.textContent="Eliminar",u.ondblclick=function(){!function(t){Swal.fire({title:"Eliminar tarea?",showCancelButton:!0,confirmButtonText:"Si",cancelButtonText:"No"}).then((a=>{a.isConfirmed&&async function(t){const{estado:a,id:n,nombre:r}=t,c=new FormData;c.append("id",n),c.append("nombre",r),c.append("estado",a),c.append("proyectoId",i());try{const a="/api/tarea/eliminar",n=await fetch(a,{method:"POST",body:c}),r=await n.json();r.resultado&&(Swal.fire("Eliminado!",r.resultado.mensaje,"success"),e=e.filter((e=>e.id!==t.id)),o())}catch(e){}}(t)}))}({...t})},s.appendChild(l),s.appendChild(u),a.appendChild(n),a.appendChild(s);document.querySelector("#listado-tareas").appendChild(a)}))}function r(t=!1,a={}){const n=document.createElement("DIV");n.classList.add("modal"),n.innerHTML=`\n            <form class="formulario nueva-tarea">\n                <legend>${t?"Editar":"Agregar"} una tarea</legend>\n                <div class="campo">\n                    <label>Tarea</label>\n                    <input\n                        type="text"\n                        name="tarea"\n                        placeholder="${a.nombre?"Editar la tarea":"Agregar tarea al proyecto actual"}"\n                        id="tarea"\n                        value="${a.nombre?a.nombre:""}"\n                    />\n                </div>\n                <div class="opciones">\n                    <input type="submit" class="submit-nueva-tarea" value="${t?"Editar":"Agregar"} tarea"/>\n                    <button type="button" class="cerrar-modal">Cancelar</button>\n                </div>\n            </form>\n        `,setTimeout((()=>{document.querySelector(".formulario").classList.add("animar")}),0),n.addEventListener("click",(function(r){if(r.preventDefault(),r.target.classList.contains("cerrar-modal")){document.querySelector(".formulario").classList.add("cerrar"),setTimeout((()=>{n.remove()}),500)}if(r.target.classList.contains("submit-nueva-tarea")){const n=document.querySelector("#tarea").value.trim();if(""===n)return void c("El nombre de la tarea es obligatorio","error",document.querySelector(".formulario legend"));t?(a.nombre=n,d(a)):async function(t){const a=new FormData;a.append("nombre",t),a.append("proyectoId",i());try{const n="/api/tarea",r=await fetch(n,{method:"POST",body:a}),d=await r.json();if(c(d.mensaje,d.tipo,document.querySelector(".formulario legend")),"exito"===d.tipo){const a=document.querySelector(".modal");setTimeout((()=>{a.remove()}),3e3);const n={id:String(d.id),nombre:t,estado:"0",proyectoId:d.proyectoId};e=[...e,n],o()}}catch(e){console.log(e)}}(n)}})),document.querySelector(".dashboard").appendChild(n)}function c(e,t,a){const n=document.querySelector(".alerta");n&&n.remove();const o=document.createElement("DIV");o.classList.add("alerta",t),o.textContent=e,a.parentElement.insertBefore(o,a.nextElementSibling),setTimeout((()=>{o.remove()}),5e3)}async function d(t){const{estado:a,id:n,nombre:r,proyectoId:c}=t,d=new FormData;d.append("id",n),d.append("nombre",r),d.append("estado",a),d.append("proyectoId",i());try{const t="/api/tarea/actualizar",c=await fetch(t,{method:"POST",body:d}),i=await c.json();if("exito"===i.respuesta.tipo){Swal.fire(i.respuesta.mensaje,i.respuesta.mensaje,"success");const t=document.querySelector(".modal");t&&t.remove(),e=e.map((e=>(e.id===n&&(e.estado=a,e.nombre=r),e))),o()}}catch(e){console.log(e)}}function i(){const e=new URLSearchParams(window.location.search);return Object.fromEntries(e.entries()).id}a.forEach((e=>{e.addEventListener("input",n)}))}();