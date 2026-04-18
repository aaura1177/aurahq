@extends('admin.layout.master')

@section('content')


<div id="dashboard-page">

<!-- Edit Popup (single edit) -->

<div class="popup-overlay" onclick="removePopupEdit(event)" id="popupEdit">
  <div class="popup-content" id="popupUpInnerEdit">
    <h3 class="popup-heading">this is the popup heading</h3>
    <button class="close-btn" id="closePopupBtnEdit" onclick="handleEditClose(this)">&times;</button>
    <div id="form_container" style="padding: 0;">
      <!-- <form action="" id="" class=""> -->
      <form id="editUserForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
       <div class="form-input">
        <label for="editname" >Name:</label>
        <input class="data-inp" type="text" name="name" id="editname" class="data-inp" required>
        
       </div>

       <div class="form-input form-input-error">
        <label for="editemail">Email<span class="error">*</span></label>
        <input class="data-inp" type="text" name="email"  id="editemail" class="data-inp" required>
        <!-- <span class="error font-8">
          didn't match the passord*
      </span> -->
       </div>

       <div class="form-input">
        <label for="editphone">Phone no:</label>
        <input class="data-inp" type="text" name="phone_no" id="editphone" class="data-inp" required>
        
       </div>

       <div class="form-input">
        <label for="editaddress">Address:</label>
        <textarea class="data-text" name="address" id="editaddress"  type="text"  class="data-inp" resize="both"></textarea>
       
       </div>
       <div class="form-input">
        <label for="editprofile">Profile Picture</label>
        <input class="data-inp" name="profile_picture" id="editProfile" type="file" class="data-inp">
       </div>
       <div class="form-input">
  <label for="editstatus">Status:</label>
  <select class="data-inp" name="status" id="editstatus" required>
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
  </select>
</div>
       

       <div class="form-submit-container">
        <button type='submit' class="btn success"><i class="fa fa-check over-hid"></i>Submit</button>
        <button class="btn danger" onclick="handleEditClose(this)"><i class="fa fa-circle-xmark over-hid"></i>Close</button>

       </div>

      </form>
     
    </div>
  </div>
</div>
<!-- edit poup  ends (single edit)  -->


<!-- delete popup -->
<div class="popup-overlay" onclick="removePopupDelete(event)" id="popupDelete">
<div class="popup-content width-50" id="popupUpInnerDelete">
  <h3 class="popup-heading">Are you sure to delete the record</h3>
  <button class="close-btn" id="closePopupBtnDelete" onclick="handleDeleteClose(this)">&times;</button>
  <div id="form_container" style="padding: 0;">
  <form id="deleteUserForm" method="POST">
  @csrf
  @method('DELETE')

  <div class="form-submit-container">
    <button type="submit" class="btn success">
      <i class="fa fa-check over-hid"></i>Yes
    </button>
    <button type="button" class="btn danger" onclick="handleDeleteClose(this)">
      <i class="fa fa-x-mark"></i>No
    </button>
  </div>
</form>

   
  </div>
</div>
</div>

<!-- delete popup ends  -->






<div id="include_all_data_wrapper">
<div id="table_data_page">
  <h3 class="main-text-h3">Users Data</h3>
  <div id="buttons_container">
    <!-- <a href="/add/user"><button class="btn success"><i class="fa fa-plus"></i>Create User</button></a> -->
    <!-- <button class="btn edit"><i class="fa fa-edit"></i> Edit Data</button>
    <button class="btn danger"><i class="fa fa-trash"></i> Delete Data</button>
    <button class="btn blue_bgc"><i class="fa fa-file"></i> Import Data</button>
    <button class="btn download_bgc"><i class="fa fa-download"></i> Download Data</button> -->
  </div>


  <div class="table-functionality">
    <div class="page-select">
      <span>select pages</span>
      <select class="data-select" onchange="handleSelect(this)" name="" id="">
       
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="500">500</option>
      </select>
    </div>

    <div class="pagination-container">

      <!-- <div class=."pagination-box">                  -->
         <!-- <button class="btn"><i class="fa fa-arrow-left"></i>Prev</button><span class="blue_bgc">1</span><span>2</span><button class="btn"><i class="fa fa-arrow-right"></i>next</button> -->
      <!-- </div> -->
    </div>

    <!-- <div class="search-bar">
      <input type="search" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" class="data-inp width-100" placeholder="Search anything...">
      <button class="btn success" style="padding-inline: .5rem; border-top-right-radius: .3rem; border-bottom-right-radius: .3rem;"> <i style="font-size: .8rem;overflow: visible;" class="fa fa-search"></i></button>
    </div> -->
  </div>

  <div id="data_container">
    <h3  class="main-text-h3">Users Data Table</h3>
    <div id="table_container">
      <table id="data_table">
        <thead>
          <tr>
            <th>Sr no.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone No.</th>
            <th>Address</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        @php $i = 1; @endphp
        @foreach($users as $user)
          <tr>
        <td>{{ $i++ }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->phone_no }}</td>
        <td>{{ $user->address }}</td>
        <td>{{ $user->status }}</td>
            <td class="action-bar">
              <div class="action-wrapper">
                <button class="btn blue_bgc p-5" onclick="handleActionButton(this)"><i class="fa fa-caret-down"></i> Action</button>
              <ul class="action-dropdown display-zero">
                <!-- <li onclick='handleTableSideEdit(this)'
         ><i class="fa fa-edit"></i>Edit</li> -->
        <li onclick='handleTableDelete(this)' data-id="{{ $user->id }}"><i class="fa fa-trash"></i>Delete</li>
<a href="{{ route('admin.projects.all', ['user_id' => $user->id]) }}">
  <li style="color: white; text-decoration: none;"> User Projects</li>
</a>              
  </ul>
         </div>
          </td>
</tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


  <div class="table-functionality">
    <div class="page-select">
      <span>select pages</span>
      <select class="data-select" onchange="handleSelect(this)" name="" id="">
       
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="500">500</option>
      </select>
    </div>

    <div class="pagination-container">

    </div>
    <!-- <div class="search-bar">
      <input type="search" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" class="data-inp width-100" placeholder="Search anything...">
      <button class="btn success" style="padding-inline: .5rem; border-top-right-radius: .3rem; border-bottom-right-radius: .3rem;"> <i style="font-size: .8rem;overflow: visible;" class="fa fa-search"></i></button>
    </div> -->
  </div>

</div>
<footer>
  <p>All rights are reserved by Aurateria Blogs</p>
</footer>
</div>



</div>


<script>



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



function handleTableDelete(element) {
    const id = element.getAttribute('data-id');
    const deletePopup = document.getElementById("popupDelete");
    const popupUpInnerDelete = document.getElementById("popupUpInnerDelete");

    const form = document.getElementById('deleteUserForm');
    form.action = `/users/delete/${id}`;

    deletePopup.classList.add("active");
    setTimeout(() => {
        popupUpInnerDelete.classList.add("active");
    }, 300);
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
</script>


@endsection