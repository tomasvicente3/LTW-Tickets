const filterSelect = document.getElementById('filter');
const sortSelect = document.getElementById('sort');
const hashtagInput = document.getElementById('HashtagInput');
const statusSelection = document.getElementById('StatusSelection');
const prioritySelection = document.getElementById('PrioritySelection');
const departmentInput = document.getElementById('DepartmentSelection');
const agentInput = document.getElementById('AgentSelection');
const submitButton = document.getElementById('SubmitFilterTicket');
const hashtagLabel = document.getElementById('HashtagSelection');

function showSubmitButton() {
  submitButton.style.display = 'block';
}

filterSelect.addEventListener('change', function () {
  const selectedValue = this.value;

  hashtagInput.style.display = 'none';
  statusSelection.style.display = 'none';
  prioritySelection.style.display = 'none';
  submitButton.style.display = 'none';
  hashtagLabel.style.display = 'none';
  departmentInput.style.display = 'none';
  agentInput.style.display = 'none';

  if (selectedValue === 'hashtag') {
    hashtagInput.style.display = 'block';
    showSubmitButton();
    hashtagLabel.style.display = 'inline-block';
  } else if (selectedValue === 'status') {
    statusSelection.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'priority') {
    prioritySelection.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'department') {
    departmentInput.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'agent') {
    agentInput.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'all') {
    showSubmitButton();
  }
  document.cookie = "filterValue=" + encodeURIComponent(selectedValue) + "; path=/";
});

sortSelect.addEventListener('change', function () {
  const selectedValue = this.value;

  if (selectedValue === 'statusSort' || selectedValue === 'prioritySort' || selectedValue === 'date') {
    showSubmitButton();
  }
  document.cookie = "sortValue=" + encodeURIComponent(selectedValue) + "; path=/";
});

function handleFilterChange(selectedValue) {
  if (selectedValue === 'hashtag') {
    hashtagInput.style.display = 'block';
    hashtagLabel.style.display = 'inline-block';
    showSubmitButton();
  } else if (selectedValue === 'status') {
    statusSelection.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'priority') {
    prioritySelection.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'department') {
    departmentInput.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'agent') {
    agentInput.style.display = 'block';
    showSubmitButton();
  } else if (selectedValue === 'all') {
    showSubmitButton();
  }
}

handleFilterChange(filterSelect.value);

