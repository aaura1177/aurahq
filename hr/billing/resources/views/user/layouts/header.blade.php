
<nav id="navbar">
    <div id="main_logo_container" class="navbar-logo-container-max">
      <img src="{{ asset('logo.png') }}"  alt="logo_web_site"  style="width: 4rem;"/>
    </div>
    <div id="navbar_side" class="navbar-side-max">
      <span id="hamburger_container">
        <div id="hamburger_logo" onclick="handleHamburger(this)">
          <i class="fa fa-bars"></i>
        </div>
      </span>

      <ul id="profile_notification_container">

        <li>
          <div id="user_profile" class="no-class" onclick="handleProfileIcon(this)">
             test
        <!-- <img src="{{ session('profile_picture') ? asset('uploads/profiles/' . session('profile_picture')) : asset('images/default-profile.png') }}" /> -->



        
        
        
        </div>
        </li>
        <div id="profile_setup_popup" class="display-zero">
          <div class="profile-popup-container">
            <div class="profile-popup-inner">

            
              <i class="fa fa-user"></i>
<center id="user_name">{{ session('user_name') }}</center>
              
              <!-- <button id="change_password"><a href="{{ url('/user/password/change') }}">CHANGE PASSWORD </a></button> -->
          </div>
          <div class="profile-features">
            <button id="profile" class="blue-bg-button"><a href="{{ url('/user/profile') }}"style="color: white; text-decoration: none;">Profile</a></button>
            <button id="sign_out" class="blue-bg-button"><a href="{{ url('/user/logout') }}"style="color: white; text-decoration: none;">Sign Out</a></button>
          </div>
          </div>
        </div>
      </ul>


    </div>
  </nav>
