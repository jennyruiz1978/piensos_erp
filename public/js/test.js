

   //////////probando add dias
   const dias = document.getElementById('dias');
    
   if(dias){

     dias.addEventListener('change', function () {
       
       let dias_selected = dias.value;       
       
       if(dias_selected != ''){          
                   
         let filaDinamica = 1;
         var numFilas = $("#tabla_dias_cond div.fila_dias_cobro").length;
         console.log('numFilas',numFilas);
         if (numFilas && numFilas > 0) {
             let fila = $("#tabla_dias_cond").find("div.fila_dias_cobro").last();        
             console.log('fila',fila);                    
             console.log('fila find',fila.find('div.numero_orden')); 
             //filaDinamica = fila.find('input').eq(0).val();                   
             let x = fila.find('div.numero_orden');              
             console.log('x',x[0].innerHTML);
             filaDinamica = x[0].innerHTML;
         } else {
             filaDinamica = 0;
         }
         
         var filaOrden = parseInt(filaDinamica) + 1;    
               

         let row = "<div class='fila_dias_cobro' id='fila_dias_"+filaOrden+"'><div class='numero_orden'>"+filaOrden+"</div><div class='contInputDias'><input class='inputSinBorde' name='diasSelected[]' value='"+dias_selected+"'> <span> dies </span> </div><span style='cursor:pointer;color:red; font-size:0.8rem;'  class='eliminar_dias_cliente' data-numord='"+filaOrden+"'>X</span></div>";

         $('#tabla_dias_cond').append(row);          

       }

     });      
   }

   var tabla_dias_cond = document.getElementById('tabla_dias_cond');   
   if(tabla_dias_cond){
     tabla_dias_cond.addEventListener("click", (event) => {
     
       const clickedBtnDel = event.target;
             
       if (clickedBtnDel.matches('.eliminar_dias_cliente')) {                  
         
         let numord= clickedBtnDel.dataset.numord;                    
         console.log('numord',numord);

         const filaDelete = document.getElementById('fila_dias_'+numord); 
         filaDelete.remove();
         
       }
             
     });
   }
 
   //////////