 

    function handleTableSideEdit(element){
        const popupUpInnerEdit = document.getElementById("popupUpInnerEdit");
        const closePopupBtnEdit = document.getElementById("closePopupBtnEdit");
        popupEdit.classList.add("active");
        setTimeout(()=>{
          popupUpInnerEdit.classList.add("active");
          
        },300)
      }
      
      function handleEditClose(element){
        const popupUpInnerEdit = document.getElementById("popupUpInnerEdit");
        const popupEdit = document.getElementById("popupEdit");
        popupUpInnerEdit.classList.remove("active");
        setTimeout(() => {
          popupEdit.classList.remove("active");
        }, 300);
      }
      
      
      function removePopupEdit(e){
        const popupUpInnerEdit = document.getElementById("popupUpInnerEdit");
      const popupEdit = document.getElementById("popupEdit");
      if (e.target === popupEdit) {
          popupUpInnerEdit.classList.remove("active");
          setTimeout(() => {
            popupEdit.classList.remove("active");
          }, 300); 
        }
    }
      
  
  
    function handleTableDelete(element){
      
      
      const deletePopup = document.getElementById("popupDelete");
      const popupUpInnerDelete = document.getElementById("popupUpInnerDelete");
     
      
      deletePopup.classList.add("active");
      setTimeout(()=>{
        popupUpInnerDelete.classList.add("active");
        
      },300)
    }
    
    function handleDeleteClose(element){
      const deletePopup = document.getElementById("popupDelete");
      const popupUpInnerDelete = document.getElementById("popupUpInnerDelete");
      popupUpInnerDelete.classList.remove("active");
        setTimeout(() => {
          deletePopup.classList.remove("active");
        }, 300);
      }
  
      
    function removePopupDelete(e){
      const deletePopup = document.getElementById("popupDelete");
      const popupUpInnerDelete = document.getElementById("popupUpInnerDelete");
      if (e.target === deletePopup) {
        popupUpInnerDelete.classList.remove("active");
          setTimeout(() => {
            deletePopup.classList.remove("active");
          }, 300); 
        }
    }
      
  
  
   
  
  
    function checkCheckBoxes(){
      let checkboxes = document.getElementsByClassName('single-checkbox');
      checkboxes = [...checkboxes];
      let count = 0 ;
      const dataIds = []
      checkboxes.forEach((elem)=>{
        elem.checked ?? count ++;
        elem.checked ?? dataIds.push(elem.dataset.id);
      })
      console.log(dataIds , count);
  
      
    }
     
  