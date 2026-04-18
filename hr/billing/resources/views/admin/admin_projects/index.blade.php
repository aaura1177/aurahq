@extends('admin.layout.master')

@section('content')


<div id="dashboard-page">

<!-- Edit Popup (single edit) -->

<div class="popup-overlay" onclick="removePopupEdit(event)" id="popupEdit">
  <div class="popup-content" id="popupUpInnerEdit">
    <h3 class="popup-heading">this is the popup heading</h3>
    <button class="close-btn" id="closePopupBtnEdit" onclick="handleEditClose(this)">&times;</button>
    <div id="form_container" style="padding: 0;">
       <form  id="editProjectFrom"  method="POST" enctype="multipart/form-data" >
        @csrf
        @method('PUT')
       <div class="form-input">
        <label for="editprojectname" >Project Name:</label>
        <input class="data-inp" type="text" name="title" id="editprojectname"  class="data-inp" required>
        
       </div>

       <div class="form-input form-input-error">
        <label for="editstart_date">Start Date<span class="error">*</span></label>
        <input class="data-inp" type="text" name="start_date" id="editstart_date"   class="data-inp" required>
        <span class="error font-8">
      </span>
       </div>

       <div class="form-input">
        <label for="editEnd_date">End Date</label>
        <input class="data-inp" type="text" name="end_date" id="editend_date" class="data-inp"  required >
        
       </div>

       <div class="form-input">
        <label for="editdiscription">Project Description:</label>
        <textarea class="data-text" name="description" id="editdiscription"  class="data-inp" resize="both"></textarea>
       
       </div>
       <div class="form-input">
        <label for="editclientname">Client Name</label>
        <input class="data-inp" type="text" name="client_name" id="editclientname" class="data-inp"  required >

       
       </div>

       <div class="form-input">
        <label for="editattachment">Attachment</label>
       <input type="file" class="data-inp" name="attachment"  id="editattachment" class="data-inp">
       <span class="error">
       </span>
       
       </div>
       <!-- <div class="form-input">
  <label for="edituser">Select User:</label>
  <select class="data-inp" name="user_id" id="edituser_id" >
    <option value="">Select User</option>
    @foreach($users as $user)
      <option value="{{ $user->id }}">{{ $user->name }}</option>
   
    @endforeach
  </select>
</div> -->
       <div class="form-input">
  <label for="editstatus">Status:</label>
  <select class="data-inp" name="status" id="editstatus" >
    <option value="Pending">Pending</option>
    <option value="In-Process">In Process</option>
    <option value="Completed">Completed</option>
    <option value="Active">Active</option>
    <option value="Inactive">Inactive</option>

  </select>
</div>
  
       <div class="form-submit-container">
        <button class="btn success"><i class="fa fa-check over-hid"></i>Submit</button>
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
    <form action="" id="deleteUserForm" class="" method="POST">
      @csrf
      @method('DELETE')
    
  
     <div class="form-submit-container">
      <button class="btn success"><i class="fa fa-check over-hid"></i>Yes</button>
      <button class="btn danger" onclick="handleDeleteClose(this)"><i class="fa fa-x-mark"></i>No</button>

     </div>

    </form>
   
  </div>
</div>
</div>

<!-- delete popup ends  -->






<div id="include_all_data_wrapper">
<div id="table_data_page">
  <h3 class="main-text-h3">Projects</h3>
  <div id="buttons_container">
    <a href="{{ route('admin.project.index') }}"><button class="btn success"><i class="fa fa-plus"></i> Add New Project</button></a>
   
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

      <!-- <div class=."pagination-box">                 
         <<button class="btn"><i class="fa fa-arrow-left"></i>Prev</button><span class="blue_bgc">1</span><span>2</span><button class="btn"><i class="fa fa-arrow-right"></i>next</button> -->
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
            <th>Sr no</th>
         
            <th>Project Title</th>
            <th>Project Description</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Client Name</th>
            <th>Attactment</th>
            <th>Status</th>
           <!-- <th>Total Working Time</th>  -->
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        @foreach ($projects as $index => $project)
          <tr>

            <td>{{ $index + 1 }}</td>
       
            <td>{{ $project->title }}</td>
            <td>{{ $project->description }}</td>
            <td>{{\Carbon\Carbon::parse( $project->start_date)->format('d-m-Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</td>
            <td>{{ $project->client_name }}</td>

            <td>
    @if($project->attachment)
        <a href="{{ asset($project->attachment) }}" target="_blank">View Attachment</a>
    @else
        No Attachment
    @endif
</td>
<!--             
            <td>
              @if($project->attachment)
              <a href="{{ asset('uploads/attachment/' . basename($project->attachment)) }}" target="_blank">            View Attachment
                </a>
                @else
                No Attachment
                @endif
              </td> -->
              <td>{{ $project->status }}</td>
            <!-- <td>{{ $project->formatted_working_time }}</td> -->
            <td class="action-bar">
              <div class="action-wrapper">
                <button class="btn blue_bgc p-5" onclick="handleActionButton(this)"><i class="fa fa-caret-down"></i> Action</button>
              <ul class="action-dropdown display-zero">
                <li onclick='handleTableSideEdit(this)'
                 data-project_id="{{ $project->id }}"
                data-id="{{ $project->user_id }}"
                data-project_name ="{{ $project->title }}"
                data-start_date ="{{ $project->start_date }}"
                data-end_date ="{{ $project->end_date }}"
                data-description ="{{ $project->description }}"
                data-client_name ="{{ $project->client_name }}"
                data-attachment ="{{ $project->attachment }}"
                data-status ="{{ $project->status }}"
                data-user ="{{ $project->user }}"
                ><i class="fa fa-edit"></i>Edit</li>
<!-- 
                <li onclick="handleTableSideEdit({{ $project->id }}, '{{ $project->title }}', '{{ $project->description }}', '{{ $project->start_date }}', '{{ $project->end_date }}', '{{ $project->client_name }}', '{{ $project->attachment }}')">
                <i class="fa fa-edit"></i> Edit -->
            <!-- </li> -->
                <li onclick='handleTableDelete(this)'  data-id="{{ $project->id }}"><i class="fa fa-trash"></i>Delete</li>
                <li>

    <a href="{{ route('assign.user', ['project_id' => $project->id]) }}"  style="color: white; text-decoration: none;">
       View Assigned Users
    </a>   
</li>
      <li>

    <a href="{{ route('user_working_timeing'  ,['project_id' => $project->id])  }}"  style="color: white; text-decoration: none;">
     User Assign Project Timeings
    </a>
   
</li>
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
<!-- 
    <div class="search-bar">
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






const project_id = element.getAttribute('data-project_id');  
  const baseUpdateUrl = "{{ url('/admin/project/update') }}";  
  

  document.getElementById('editprojectname').value = element.getAttribute('data-project_name');
  document.getElementById('editstart_date').value = element.getAttribute('data-start_date');
  document.getElementById('editend_date').value = element.getAttribute('data-end_date');
  document.getElementById('editdiscription').value = element.getAttribute('data-description');
  document.getElementById('editclientname').value = element.getAttribute('data-client_name');
  document.getElementById('editstatus').value = element.getAttribute('data-status');

  
  const form = document.getElementById("editProjectFrom");
  form.action = `${baseUpdateUrl}/${project_id}`;  


  
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
    form.action = `/project/delete/${id}`;

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