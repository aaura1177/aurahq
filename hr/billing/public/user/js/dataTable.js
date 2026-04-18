function handleActionButton(element) {
  let parentOfThePopup = element.parentNode;
  let actionPopup =
    parentOfThePopup.getElementsByClassName("action-dropdown")[0];

  let iTag = element.getElementsByTagName("i")[0];
  let totalActionPopup = document.querySelectorAll(".action-dropdown");
  totalActionPopup = [...totalActionPopup];
  totalActionPopup.forEach((elem) => {
    if (elem !== actionPopup) {
      if (!elem.classList.contains("display-zero")) {
        elem.classList.add("display-zero");
        elem.classList.remove("display-one");
        let iTag = elem.parentNode
          .getElementsByTagName("button")[0]
          .getElementsByTagName("i")[0];
        iTag.classList.add("fa-caret-down");
        iTag.classList.remove("fa-caret-up");
      }
    }
  });
  if (actionPopup.classList.contains("display-zero")) {
    actionPopup.classList.remove("display-zero");
    iTag.classList.remove("fa-caret-down");
    iTag.classList.add("fa-caret-up");
    actionPopup.classList.add("display-one");
  } else {
    actionPopup.classList.add("display-zero");
    actionPopup.classList.remove("display-one");
    iTag.classList.add("fa-caret-down");
    iTag.classList.remove("fa-caret-up");
  }
}

// table functionality

window.onload = () => {
  let element1 = document.getElementsByClassName("data-select")[0];
  handlePaginationInitially();
  handleSelect(element1);
};

// handling the select of no of the data
function handleSelect(element) {
  let tr = document.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
  let totalTr = tr.length;
  let selectedRows = Number(element.value);

  for (let i = 0; i < totalTr; i++) {
    if (i < selectedRows) {
      if (tr[i].classList.contains("display-zero")) {
        tr[i].classList.remove("display-zero");
      }
    } else {
      if (!tr[i].classList.contains("display-zero")) {
        tr[i].classList.add("display-zero");
      }
    }
  }

  //matching the value of both the selects 
  let element1 = document.getElementsByClassName("data-select")[0];
  let element2 = document.getElementsByClassName("data-select")[1];
  if(element === element1){
    document.getElementsByClassName("data-select")[1].value = element1.value;
}
else{
      document.getElementsByClassName("data-select")[0].value = element2.value;

  }
  handlePagination(null,1);
}

// handling the paginiation
function handlePaginationInitially() {
  let tr = document.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
  let selectedRows = document.getElementsByClassName("data-select")[0].value;

  let totalSpan = Number(tr.length / selectedRows);

  let totalSpanNew = parseInt(Number(tr.length / selectedRows));
  let totalSpanFinal = parseInt(totalSpan);
  let reminder = totalSpan - totalSpanNew;

  if (reminder > 0) {
    totalSpanFinal++;
  }
  let code_html = `  <button class="btn" disabled onclick ='handlePrevPage(this)'><i class="fa fa-arrow-left"></i>Prev</button>`;

  if (totalSpanFinal > 6) {
    for (let i = 0; i < 7; i++) {
      if (i < 3) {
        if (i === 0) {
          code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
            i + 1
          }</span>`;
        } else {
          code_html += `<span onclick='handlePagination(this)'>${i + 1}</span>`;
        }
      } else if (i === 3) {
        code_html += `...`;
      } else {
        code_html += `<span onclick='handlePagination(this)'>${totalSpanFinal - (7 - i - 1)} </span>`;
      }
    }
  } else {
    for (let i = 0; i < totalSpanFinal; i++) {
      if (i === 0) {
        code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
          i + 1
        }</span>`;
      } else {
        code_html += `<span onclick='handlePagination(this)'>${i + 1}</span>`;
      }
    }
  }

  code_html += '<button  class="btn" onclick ="handleNextPage(this)">next<i class="fa fa-arrow-right"></button>';

  let pagination_container1 = document.getElementsByClassName(
    "pagination-container"
  )[0];
  let pagination_container2 = document.getElementsByClassName(
    "pagination-container"
  )[1];
  let div = document.createElement("div");
  div.classList.add("pagination-box");
  div.innerHTML = code_html;

  let newDiv = document.createElement("div");
  newDiv.innerHTML = code_html;
  newDiv.classList.add("pagination-box");
  pagination_container1.appendChild(div);
  pagination_container2.appendChild(newDiv);

  pagination_container1.append;
}

// after any page is selected
function handlePagination(element,newPage='default') {
    console.log(newPage);
    console.log(element);
    let currentPage = null;
    if(element !== null){
        currentPage = element.innerHTML;

    }
    else{
        currentPage = newPage;
    }
    
  currentPage = Number(currentPage);
  let code_html = ``;
  if (currentPage === 1) {
    code_html += `  <button class="btn" disabled onclick ="handlePrevPage(this)"><i class="fa fa-arrow-left"></i>Prev</button>`;
  } else {
    code_html += `<button class="btn" onclick ="handlePrevPage(this)"><i class="fa fa-arrow-left"></i>Prev</button>`;
  }
  let privious_pagination_container1 = document.getElementsByClassName(
    "pagination-container"
  )[0];
  let privious_pagination_box1 =
    privious_pagination_container1.getElementsByClassName("pagination-box")[0];
  privious_pagination_container1.removeChild(privious_pagination_box1);

  let privious_pagination_container2 = document.getElementsByClassName(
    "pagination-container"
  )[1];
  let privious_pagination_box2 =
    privious_pagination_container2.getElementsByClassName("pagination-box")[0];
  privious_pagination_container2.removeChild(privious_pagination_box2);

  let tr = document.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
  let selectedRows = document.getElementsByClassName("data-select")[0].value;

  let totalSpan = Number(tr.length / selectedRows);

  let totalSpanNew = parseInt(Number(tr.length / selectedRows));
  let totalSpanFinal = parseInt(totalSpan);
  let reminder = totalSpan - totalSpanNew;
  if (reminder > 0) {
    totalSpanFinal++;
  }
  console.log(totalSpanFinal, "totalSpan");
  console.log(currentPage, "selected_rows");
  if (totalSpanFinal > 6) {
    for (let i = 0; i < 8; i++) {
     

      if (currentPage <= 4) {
        if(i < 5 && i !== currentPage - 1){
            code_html += `<span onclick='handlePagination(this)'>${
                i + 1
              }</span>`
        } 
        else if (i === currentPage - 1) {
          code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
            i + 1
          }</span>`;
        }
       else if (i === 5) {
            code_html += `...`;
          } 
        else {
            code_html += `<span onclick='handlePagination(this)'>${
              totalSpanFinal - (8 - i - 1)
            }</span>`;
          }
      } 
      else if (currentPage > 4 && currentPage < totalSpanFinal - 2){
        
        if(i < 1){
            code_html += `<span  onclick='handlePagination(this)'>${
                i + 1
              }</span>`;
        }
        else if (i === 1){
            code_html += `...`;
        }
        else if (i === 2){
            code_html += `<span  onclick='handlePagination(this)'>${
                currentPage - 1
              }</span>`;
            
            
        }
        else if(i === 3){
            code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
                currentPage
              }</span>`;
        }
        else if(i === 4){
            code_html += `<span  onclick='handlePagination(this)'>${
                currentPage + 1
              }</span>`;
        }
        else if(i === 5){
            code_html += `...`;
        }
        else {
            code_html += `<span onclick='handlePagination(this)'>${
                totalSpanFinal - (8 - i - 1)
              }</span>`;
        }

      }
      else{
        if(i < 3){
            code_html += `<span  onclick='handlePagination(this)'>${
                i + 1
              }</span>`;
        }
        else if (i === 3 || i === 4){
            code_html += `...`;
        }
        else if (i > 4){
       

              if(currentPage === totalSpanFinal){
                if(i === 7){
                    code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
                        currentPage
                      }</span>`;
                }
                else{
                    code_html +=`<span  onclick='handlePagination(this)'>${
                        totalSpanFinal - (8 - i - 1)
                      }</span>`;
                }
              }
              else if(currentPage === totalSpanFinal - 1){
                
                if(i === 6){
                    code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
                        currentPage
                      }</span>`;
                }
                else{
                    code_html +=`<span  onclick='handlePagination(this)'>${
                        totalSpanFinal - (8 - i - 1)
                      }</span>`;
                }
              }
              else{
                if(i === 5){
                    code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
                        currentPage
                      }</span>`;
                }
                else{
                    code_html +=`<span onclick='handlePagination(this)'>${
                        totalSpanFinal - (8 - i - 1)
                      }</span>`;
                }
              }
            
            
        }
        
        else {
            code_html += `<span onclick='handlePagination(this)'>${
                totalSpanFinal - (8 - i - 1)
              }</span>`;
        }
      }
 
      
    }
  } else {
    for (let i = 0; i < totalSpanFinal; i++) {
      if (i === currentPage - 1) {
        code_html += `<span class='blue_bgc' onclick='handlePagination(this)'>${
          i + 1
        }</span>`;
      } else {
        code_html += `<span onclick='handlePagination(this)'>${i + 1}</span>`;
      }
    }
  }


  if(currentPage === totalSpanFinal){

      code_html += '<button class="btn" disabled onclick ="handleNextPage(this)">next<i class="fa fa-arrow-right"></button>';
  }
  else{
      code_html += '<button class="btn" onclick ="handleNextPage(this)">next<i class="fa fa-arrow-right"></button>';

  }

  console.log(code_html);
  

  let pagination_container1 = document.getElementsByClassName(
    "pagination-container"
  )[0];
  let pagination_container2 = document.getElementsByClassName(
    "pagination-container"
  )[1];
  let div = document.createElement("div");
  div.classList.add("pagination-box");
  div.innerHTML = code_html;

  let newDiv = document.createElement("div");
  newDiv.innerHTML = code_html;
  newDiv.classList.add("pagination-box");
  pagination_container1.appendChild(div);
  pagination_container2.appendChild(newDiv);

  pagination_container1.append;

  let dataToShowMin = Number(selectedRows * (currentPage - 1));
  let dataToShowMax = Number(selectedRows * currentPage);

  for (let i = 0; i < tr.length; i++) {
    if (tr[i]) {
      if (i < dataToShowMin) {
        if (!tr[i].classList.contains("display-zero")) {
          tr[i].classList.add("display-zero");
        }
      } else if (i >= dataToShowMin && i < dataToShowMax) {
        if (tr[i].classList.contains("display-zero")) {
          tr[i].classList.remove("display-zero");
        }
      } else {
        if (!tr[i].classList.contains("display-zero")) {
          tr[i].classList.add("display-zero");
        }
      }
    }
  }
}



function handleNextPage(element){
    let parentContainer = element.parentNode;
   
    let currentPage = Number(parentContainer.getElementsByClassName('blue_bgc')[0].innerHTML);
    handlePagination(null,currentPage + 1);
    
}


function handlePrevPage(element){
    let parentContainer = element.parentNode;
    
    let currentPage = Number(parentContainer.getElementsByClassName('blue_bgc')[0].innerHTML);
    handlePagination(null,currentPage - 1);
    
}
