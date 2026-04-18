let dropDown_sideBar = document.getElementsByClassName("more_then_one_bar");
if(dropDown_sideBar){
  dropDown_sideBar = [...dropDown_sideBar];
  let TotalUl = document
    .getElementById("side_bar_main_ul")
    .getElementsByTagName("ul");
  TotalUl = [...TotalUl];
  dropDown_sideBar.forEach((item) => {
      
      
    item.addEventListener("click", (event) => {
      event.preventDefault();
      let ul = item.nextElementSibling;
      

      
      
      let iLogo = item.getElementsByTagName("i")[1];
      TotalUl.forEach((ulInner) => {
          
          
          if(ul !== ulInner){
              if (!ulInner.classList.contains("min-dropdown")) {
                  let iLogoInner = ulInner.previousElementSibling.getElementsByTagName('i')[1];
                 
                  
                  ulInner.classList.add("min-dropdown");
                  
                  iLogoInner.classList.add("fa-angle-down");
                  iLogoInner.classList.remove("fa-angle-up");
                }
          }
        
      });
  
      if (ul.classList.contains("min-dropdown")) {
        
        
        ul.classList.remove("min-dropdown");
        iLogo.classList.remove("fa-angle-down");
        iLogo.classList.add("fa-angle-up");
      } else {

       
        ul.classList.add("min-dropdown");
        iLogo.classList.add("fa-angle-down");
        iLogo.classList.remove("fa-angle-up");
      }
    });
  });
}

// code for the toggling of the sidebar 
function handleHamburger(element){

  if(window.innerWidth <= '768'){
    lessThanTabletView();
  }
  else{
    moreThanTabletView();
  }

}




// toggle navbar for the width more than 768 px
function moreThanTabletView(){
    let sideBar = document.getElementById('side_bar');
    let contentBar = document.getElementById('data_section');
    let navbarLeft = document.getElementById('main_logo_container');
    let navbarRight = document.getElementById('navbar_side');
    let mainNavigation = document.getElementById('main_navigation');


    if(!sideBar.classList.contains('side_bar_min')){
      // side bar toggle full to 40px 
      sideBar.classList.add('side_bar_min');
      sideBar.classList.remove('side_bar_max');

      // contentbar toggle to full - 40px
      contentBar.classList.add('data-section-min');
      contentBar.classList.remove('data-section-max');

      // navbar toggling 
      navbarLeft.classList.add('navbar-logo-container-min');
      navbarRight.classList.remove('navbar-logo-container-max');

      navbarRight.classList.add('navbar-side-min');
      navbarRight.classList.remove('navbar-side-max');
      mainNavigation.style.opacity = '0';

    }
    else{
      sideBar.classList.remove('side_bar_min');
      sideBar.classList.add('side_bar_max');

      // contentbar toggle to full - 40px
      contentBar.classList.remove('data-section-min');
      contentBar.classList.add('data-section-max');

      // navbar toggling 
      navbarLeft.classList.remove('navbar-logo-container-min');
      navbarRight.classList.add('navbar-logo-container-max');

      navbarRight.classList.remove('navbar-side-min');
      navbarRight.classList.add('navbar-side-max');
      mainNavigation.style.opacity = '1';
    }

}


// toggle navbar for the width less than 768 px
function lessThanTabletView(){
  let sideBar = document.getElementById('side_bar');
  let contentBar = document.getElementById('data_section');
  let navbarLeft = document.getElementById('main_logo_container');
  let navbarRight = document.getElementById('navbar_side');


  if(!sideBar.classList.contains('side_bar_min')){
    // side bar toggle full to 40px 
    sideBar.classList.add('side_bar_min');
    sideBar.classList.remove('side_bar_max');

    // contentbar toggle to full - 40px
    contentBar.classList.add('data-section-min');
    contentBar.classList.remove('data-section-max');

    // navbar toggling 
    navbarLeft.classList.add('navbar-logo-container-min');
    navbarRight.classList.remove('navbar-logo-container-max');

    navbarRight.classList.add('navbar-side-min');
    navbarRight.classList.remove('navbar-side-max');

  }
  else{
    sideBar.classList.remove('side_bar_min');
    sideBar.classList.add('side_bar_max');

    // contentbar toggle to full - 40px
    contentBar.classList.remove('data-section-min');
    contentBar.classList.add('data-section-max');

    // navbar toggling 
    navbarLeft.classList.remove('navbar-logo-container-min');
    navbarRight.classList.add('navbar-logo-container-max');

    navbarRight.classList.remove('navbar-side-min');
    navbarRight.classList.add('navbar-side-max');
  }
}


function handleProfileIcon(element){

  let profileContainer = document.getElementById('profile_setup_popup');

  if(profileContainer.classList.contains('display-zero')) {
    profileContainer.classList.remove('display-zero');
    profileContainer.classList.add('display-one');
  }
  else{
    profileContainer.classList.remove('display-one');
    profileContainer.classList.add('display-zero');
  }
  

}


function handleNotificationBell(element){
  let notificationContainer = document.getElementById('notification_pannel');

  if(notificationContainer.classList.contains('display-zero')) {
    notificationContainer.classList.remove('display-zero');
    notificationContainer.classList.add('display-one');
  }
  else{
    notificationContainer.classList.remove('display-one');
    notificationContainer.classList.add('display-zero');
  }
  
}



function handleSelectCheckboxAll(element){
  let checkboxes = document.getElementsByClassName('single-checkbox');
  console.log(checkboxes);
  
  checkboxes = [...checkboxes];
  if(element.checked) {
    console.log(element.checked);
    
    checkboxes.forEach((elem)=> {
      let tr = elem.closest('tr');
      !(tr.classList.contains('display-zero')) ? elem.checked = true : null;

    }
    )
  }
  else{
    console.log(element.checked);
    checkboxes.forEach((elem)=> {
      let tr = elem.closest('tr');
       elem.checked = false 

    }
    )
  }
}

