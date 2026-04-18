@extends('admin.layout.master')

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
        <!-- <div class="form-input">
          <label for="editprojectname">Project Title</label>
          <input class="data-inp" type="text" name="title" id="editprojectname" readonly required>
        </div> -->

        <!-- Status Dropdown -->
        <div class="form-input">
          <label for="editstatus">Status:</label>
          <select id="editstatus" class="data-inp"  name="status" onchange="document.getElementById('editstatusHidden').value = this.value;">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
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


<div id="include_all_data_wrapper">
<div id="table_data_page">
  <h3 class="main-text-h3">Projects Working Timeing</h3>
 <div id="buttons_container">
 
   
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
            <!-- <th><input type="checkbox" class="inp-check" id="select_all_checkbox" onclick="handleSelectCheckboxAll(this)"></th> -->
            <th>Sr no.</th>
            <th>Date</th>
            <th>User Name</th>
            <th>Project Title</th>
            <th>TapIn</th>
            <th>TapOut</th>
            <th>Total Working time</th>

            <!-- <th>Action</th> -->
          </tr>
        </thead>
        <tbody>
        @foreach($projectUsers as $index => $record)
          <tr>
             <td>{{ $index + 1 }}</td>

             <td>{{ \Carbon\Carbon::parse($record->created_at)->format('d-m-Y') }}</td>

                 <td>{{ $record->user->name ?? 'N/A' }}</td>
             
             <td>{{ $record->project->title ?? 'N/A' }}</td>
            <td>
            {{ $record->start_time ?? 'N/A' }}
            </td>
            <td>
               {{ $record->end_time ?? 'N/A' }}
            </td>



            <td>
    @if($record->start_time && $record->end_time)
        @php
            // Add today's date to time strings to safely parse
            $start = \Carbon\Carbon::parse(date('Y-m-d') . ' ' . $record->start_time);
            $end = \Carbon\Carbon::parse(date('Y-m-d') . ' ' . $record->end_time);
            $diff = $start->diff($end);
        @endphp
        {{ $diff->format('%H:%I:%S') }}
    @else
        N/A
    @endif
</td>
            <!-- <td>
            @if($record->start_time && $record->end_time)
            @php
                $start = \Carbon\Carbon::parse($record->start_time);
                $end = \Carbon\Carbon::parse($record->end_time);
                $diff = $start->diff($end);
            @endphp
            {{ $diff->format('%H:%I:%S') }}


            @else
            N/A
        @endif
      </td> -->
        
            <!-- <td class="action-bar">
              <div class="action-wrapper">
                <button class="btn blue_bgc p-5" onclick="handleActionButton(this)"><i class="fa fa-caret-down"></i> Action</button>
              <ul class="action-dropdown display-zero">
               <li onclick='handleTableSideEdit(this)'
              data-projectUser_id="{{ $record->id }}"
              data-status="{{ $record->status }}"
            >
  <i class="fa fa-edit"></i>Edit
</li> -->

                <!-- <li onclick='handleTableDelete(this)' data-id="{{ $record->id }}" ><i class="fa fa-trash"></i>Delete</li> -->
            
              <!-- </ul> --> 
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
  const baseUpdateUrl = "/admin/status/update";

  
  popupEdit.classList.add("active");
  setTimeout(() => {
    popupUpInnerEdit.classList.add("active");
  }, 300);

  


  const projectUserId = element.getAttribute('data-projectUser_id');
  const projectStatus = element.getAttribute('data-status');

  // document.getElementById('editprojectname').value = projectTitle;
  document.getElementById('editstatus').value = projectStatus;
  document.getElementById('editstatusHidden').value = projectStatus;

  
  const form = document.getElementById("editProjectForm");
  form.action = `${baseUpdateUrl}/${projectUserId}`;
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
    form.action = `/admin/assinged/project/delete/${id}`;

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