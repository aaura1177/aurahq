document.addEventListener("click", (event) => {

    
    const clickedElement = event.target;
    const elementClass = clickedElement.className; 
    console.log(elementClass);
    
    

    // for the profile popup
    let profile_setup_popup = document.getElementById('profile_setup_popup');
    if(profile_setup_popup.classList.contains('display-one')){
   
        
        if(elementClass !== 'no-class' && elementClass !== 'profile-popup-inner' && elementClass !=='user_name' && elementClass !== 'profile-features'){
            profile_setup_popup.classList.remove('display-one');
            profile_setup_popup.classList.add('display-zero');

        }
    }

    // for the action popup
    let actionPoup = document.getElementsByClassName('action-dropdown');
    actionPoup = [...actionPoup];
    actionPoup.forEach((item)=>{
        if(item.classList.contains('display-one')){

            if(elementClass !== 'fa fa-caret-up' && elementClass !=='btn blue_bgc p-5'){
                item.classList.remove('display-one');
                item.classList.add('display-zero');
                let iTag = item.parentNode.getElementsByTagName('button')[0].getElementsByTagName('i')[0];
                console.log(iTag);
                
                iTag.classList.remove('fa-caret-up');
                iTag.classList.add('fa-caret-down');
            }
        }
    })


    // for notification pannel toggel
    let notificationPannel = document.getElementById('notification_pannel');
    if(notificationPannel.classList.contains('display-one')){
   
        
        if(elementClass !== 'notification-counter' && elementClass !== 'fa fa-bell' && elementClass !=='no-class display-one'){
            notificationPannel.classList.remove('display-one');
            notificationPannel.classList.add('display-zero');

        }
    }

   
})