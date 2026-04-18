@extends('user.layouts.master')

@section('content')


<div id="dashboard-page">

<!-- Edit Popup (single edit) -->

<div class="popup-overlay" onclick="removePopupEdit(event)" id="popupEdit">
  <div class="popup-content" id="popupUpInnerEdit">
    <h3 class="popup-heading">Edit Project</h3>
    <button class="close-btn" id="closePopupBtnEdit" onclick="handleEditClose(this)">&times;</button>

    <div id="form_container" style="padding: 0;">
      <form id="editProjectForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Project Title (readonly) -->
        <div class="form-input">
          <label for="editprojectname">Project Title</label>
          <input class="data-inp" type="text" name="title" id="editprojectname" readonly required>
        </div>

        <!-- Status Dropdown -->
        <div class="form-input">
          <label for="editstatus">Status:</label>
          <select id="editstatus" class="data-inp" onchange="document.getElementById('editstatusHidden').value = this.value;">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
          </select>
          <input type="hidden" name="status" id="editstatusHidden" value="">
        </div>

        <!-- Buttons -->
        <div class="form-submit-container">
          <button type="submit" class="btn success"><i class="fa fa-check over-hid"></i>Submit</button>
          <!-- <button type="button" class="btn danger" onclick="handleEditClose(this)"><i class="fa fa-circle-xmark over-hid"></i>Close</button> -->
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
    <form action="deleteProjectForm" id="deleteProjectForm" class="" method="POST">
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


@if(Session::has('success'))
        <div class="toster success">
            <div class="toster-message">
                <p>{{ Session::get('success') }}</p>
            </div>
            <span></span>
        </div>
    @endif
 
    @if(Session::has('error'))
        <div class="toster danger">
            <div class="toster-message">
                <p>{{ Session::get('error') }}</p>
            </div>
            <span></span>
        </div>
    @endif



<div id="include_all_data_wrapper">
<div id="table_data_page">
  <h3 class="main-text-h3">Projects</h3>
 <div id="buttons_container">
    <!-- <a href="{{ route('project.index') }}"><button class="btn success"><i class="fa fa-plus"></i> Add New Project</button></a> -->
   
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

      <!-- <div class=."pagination-box"> -->
         <!-- <button class="btn"><i class="fa fa-arrow-left"></i>Prev</button><span class="blue_bgc">1</span><span>2</span><button class="btn"><i class="fa fa-arrow-right"></i>next</button> -->
      <!-- </div> -->
    </div>

    <!-- <div class="search-bar">
      <input type="search" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" class="data-inp width-100" placeholder="Search anything...">
      <button class="btn success" style="padding-inline: .5rem; border-top-right-radius: .3rem; border-bottom-right-radius: .3rem;"> <i style="font-size: .8rem;overflow: visible;" class="fa fa-search"></i></button>
    </div> -->
  </div>

  <div id="data_container">
    <h3  class="main-text-h3">Projects</h3>
    <div id="table_container">
      <table id="data_table">
        <thead>
          <tr>
            <!-- <th><input type="checkbox" class="inp-check" id="select_all_checkbox" onclick="handleSelectCheckboxAll(this)"></th> -->
            <th>Sr no.</th>
            <th>User Name</th>
            <th>Project Title</th>
            <th>Project Description</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Attactment</th>
          
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        @foreach ($projects as $index => $project)

          <tr>

             <td>{{ $index + 1 }}</td>
            <td>{{ $project->user->name }}</td>
            <td>{{ $project->title }}</td>
            <td>{{ $project->description }}</td>
            <td>{{ $project->start_date }}</td>
            <td>{{ $project->end_date }}</td>
            <td>{{ $project->status }}</td>
     
          <td>
    @if($project->attachment)
    <a href="{{ asset('uploads/attachment/' . basename($project->attachment)) }}" target="_blank">    View Attachment
        </a>
    @else
        No Attachment
    @endif
</td>
            <td class="action-bar">
              <div class="action-wrapper">
                <button class="btn blue_bgc p-5" onclick="handleActionButton(this)"><i class="fa fa-caret-down"></i> Action</button>
              <ul class="action-dropdown display-zero">
              <li onclick='handleTableSideEdit(this)'
    data-id="{{ $project->id }}"
    data-project_name="{{ $project->title }}"
    data-status="{{ $project->status }}">
  <i class="fa fa-edit"></i>Edit
</li>
<!-- <a href="{{ route('user.projects.working', ['project_id' => $project->id]) }}" style="color: white; text-decoration: none;">
  <li><i class="fa fa-clock"></i> Project Timing</li>
</a>             -->
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
function handleTableSideEdit(element) {
  const popupUpInnerEdit = document.getElementById("popupUpInnerEdit");
  const popupEdit = document.getElementById("popupEdit");
  const baseUpdateUrl = "{{ url('/updated/projected') }}";

  
  popupEdit.classList.add("active");
  setTimeout(() => {
    popupUpInnerEdit.classList.add("active");
  }, 300);

  
  const projectId = element.getAttribute('data-id');
  const projectTitle = element.getAttribute('data-project_name');
  const projectStatus = element.getAttribute('data-status');

  
  document.getElementById('editprojectname').value = projectTitle;
  document.getElementById('editstatus').value = projectStatus;
  document.getElementById('editstatusHidden').value = projectStatus;

  
  const form = document.getElementById("editProjectForm");
  form.action = `${baseUpdateUrl}/${projectId}`;
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

    const form = document.getElementById('deleteProjectForm');
    form.action = `/project/deleted/${id}`;

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