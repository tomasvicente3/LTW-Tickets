"use strict";

//Make header menu appear on click

const menuButton = document.querySelector('#Menu');
const menuPopup = document.querySelector('#FloatingSettingsBox');

if (menuPopup) {

    menuPopup.style.display = "none";

    menuButton.addEventListener('click', function () {
        if (menuPopup.style.display == "none") {
            menuPopup.style.display = "block";
        }
        else {
            menuPopup.style.display = "none";
        }
    });
}

//Right clicking on an inferior clearance user's name make upgrade popup appear

(async () => {
    function getCurrentClearance() {
        try { return fetch('../api/clearance.api.php') } catch (e) { console.error(e) }
    }

    function closeAllPopups(element) {
        const items = document.getElementsByClassName("UpgradeOptions");
        if (items.length == 0) return;

        for (let i = 0; i < items.length; i++) {

            //If the element clicked is not the list itself, its child, or its grandparent, close it
            if (element == null || (items[i] !== element.parentNode.parentNode && items[i] !== element.parentNode && items[i] !== element)) {
                items[i].parentNode.removeChild(items[i]);
            }
        }
    }

    const response = await getCurrentClearance();
    if (!response.ok) { console.error(`Error: code ${response.status}`); return; }
    const currentClearance = (await response.text());
    console.log(currentClearance);

    let inferiorNames;

    if (currentClearance == "admin") {
        inferiorNames = document.querySelectorAll(".publicAgentName, .publicClientName");
    } else if (currentClearance == "agent") {
        inferiorNames = document.querySelectorAll(".publicClientName");
    }

    if (inferiorNames) {
        for (let i = 0; i < inferiorNames.length; i++) {
            const elem = inferiorNames[i];
            elem.addEventListener("contextmenu", function (ev) {
                ev.preventDefault();

                //Remove previously open popups
                closeAllPopups();

                //Create list that will contain options
                let upgradeOptions = document.createElement("ul");
                upgradeOptions.setAttribute("class", "UpgradeOptions");
                elem.appendChild(upgradeOptions);
                elem.style.position = "relative";

                if (elem.classList.contains("publicClientName")) {
                    let agentUpgrade = document.createElement("li");
                    agentUpgrade.setAttribute("class", "AgentUpgrade");
                    agentUpgrade.innerHTML = "<p>Upgrade to Agent</p><i class=\"fa-solid fa-circle-up\"></i>"

                    agentUpgrade.addEventListener("click", function () { post("../actions/actionUpgradeToAgent.php", { id: elem.closest("[data-id]").dataset.id }); })

                    upgradeOptions.appendChild(agentUpgrade);
                }
                if (elem.classList.contains("publicAgentName") && currentClearance == "admin") {
                    let adminUpgrade = document.createElement("li");
                    adminUpgrade.setAttribute("class", "AdminUpgrade");
                    adminUpgrade.innerHTML = "<p>Upgrade to Admin</p><i class=\"fa-solid fa-circle-up\"></i>"
                    upgradeOptions.appendChild(adminUpgrade);

                    adminUpgrade.addEventListener("click", function () { post("../actions/actionUpgradeToAdmin.php", { id: elem.closest("[data-id]").dataset.id }); })

                    let agentDowngrade = document.createElement("li");
                    agentDowngrade.setAttribute("class", "AdminDowngrade");
                    agentDowngrade.innerHTML = "<p>Downgrade to Client</p><i class=\"fa-solid fa-circle-down\"></i>"
                    upgradeOptions.appendChild(agentDowngrade);

                    agentDowngrade.addEventListener("click", function () { post("../actions/actionDowngradeToClient.php", { id: elem.closest("[data-id]").dataset.id }); })
                }
            })
        }

        //Close lists when clicked off
        document.addEventListener("click", function (e) {
            closeAllPopups(e.target);
        });
    }

})();


function post(path, params, method = 'post') {

    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    for (const key in params) {

        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = key;
        hiddenField.value = params[key];

        form.appendChild(hiddenField);

    }

    document.body.appendChild(form);
    form.submit();
}

function autocomplete(input, items) {

    if (!input) return;

    let currentFocus = 0;

    input.addEventListener("input", generateAutocompleteList);
    input.addEventListener("click", generateAutocompleteList);

    input.addEventListener("keydown", function (e) {
        let list = document.getElementById(this.id + " AutocompleteList");
        if (list) list = list.getElementsByTagName("li");

        if (e.keyCode == 40) { //Down key
            addFocus(list, currentFocus, ++currentFocus);
        } else if (e.keyCode == 38) { //Up key
            //Prevent the cursos from moving to start of word
            e.preventDefault();
            addFocus(list, currentFocus, --currentFocus);
        } else if (e.keyCode == 13) { //Enter key
            //Prevent the form from being submitted
            e.preventDefault();
            if (currentFocus > -1) {
                //Simulate a click on the active item
                if (list) list[currentFocus].click();
            }
        }
    });

    //Close lists when clicked off
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });


    function generateAutocompleteList() {
        let autocompleteList, itemLi, currentValue = input.value;

        //Close any open autocomplete lists
        closeAllLists();
        if (!currentValue) { currentFocus = -1; }

        //Create list that will contain autocomplete suggestions
        autocompleteList = document.createElement("ol");
        autocompleteList.setAttribute("id", input.id + " AutocompleteList");
        autocompleteList.setAttribute("class", "AutocompleteList");

        //Append list as a child of current container
        input.parentNode.appendChild(autocompleteList);

        items.forEach(element => {
            //Check if the item starts with the same letters as the current input
            if (element.substr(0, currentValue.length).toLowerCase() == currentValue.toLowerCase() || currentValue.length == 0) {
                //Create a list item for each matching element
                itemLi = document.createElement("li");
                itemLi.setAttribute("class", "AutocompleteItem");

                if (currentValue.length == 0) {
                    itemLi.innerHTML = element;
                } else {
                    //Make matching letters bold
                    itemLi.innerHTML = "<strong>" + element.substr(0, currentValue.length) + "</strong>";
                    itemLi.innerHTML += element.substr(currentValue.length);
                }

                itemLi.addEventListener("click", function () {
                    //Insert value on input field
                    input.value = element;
                    closeAllLists();
                });
                autocompleteList.appendChild(itemLi);
            }
        });
    }

    function addFocus(list, previousFocus) {
        if (!list) return false;
        if (previousFocus > -1) {
            removeFocus(list, previousFocus);
        }

        if (currentFocus >= list.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (list.length - 1);

        list[currentFocus].classList.add("AutocompleteFocus");
    }
    function removeFocus(list, currentFocus) {
        list[currentFocus].classList.remove("AutocompleteFocus");
    }

    function removeAllFocus(list) {
        for (let i = 0; i < list.length; i++) {
            list[i].classList.remove("AutocompleteFocus");
        }
    }

    function closeAllLists(element) {
        //If input was clicked, do not close lists
        if (element == input) return;

        const items = input.parentNode.querySelectorAll(".AutocompleteList");
        if (items.length == 0) return;

        for (let i = 0; i < items.length; i++) {

            if (items[i] !== element) {
                items[i].parentNode.removeChild(items[i]);
            }
        }

        removeAllFocus(items);
    }
}

//Autocomplete for hashtags, departments and agents

async function addAutocomplete() {
    function getHashtags() {
        try { return fetch('../api/hashtags.api.php') } catch (e) { console.error(e) }
    }

    const response1 = await getHashtags();
    if (!response1.ok) { console.error(`Error: code ${response1.status}`); return; }
    let hashtags; try { hashtags = await response1.json(); } catch (e) { console.error(e); return; }

    function getDepartments() {
        try { return fetch('../api/departments.api.php') } catch (e) { console.error(e) }
    }

    const response2 = await getDepartments();
    if (!response2.ok) { console.error(`Error: code ${response2.status}`); return; }
    let departments; try { departments = await response2.json(); } catch (e) { console.error(e); return; }

    function getAgents() {
        try { return fetch('../api/agents.api.php') } catch (e) { console.error(e) }
    }

    const response3 = await getAgents();
    if (!response3.ok) { console.error(`Error: code ${response3.status}`); return; }
    let agents; try { agents = await response3.json(); } catch (e) { console.error(e); return; }

    function getFAQs() {
        try { return fetch('../api/faq.api.php') } catch (e) { console.error(e) }
    }

    const response4 = await getFAQs();
    if (!response4.ok) { console.error(`Error: code ${response4.status}`); return; }
    let faqs; try { faqs = await response4.json(); } catch (e) { console.error(e); return; }

    let hashtagInputs = document.getElementsByClassName("HashtagsAutocomplete");
    let departmentInputs = document.getElementsByClassName("DepartmentAutocomplete");
    let faqInputs = document.getElementsByClassName("FaqAutocomplete");
    let agentInputs = document.getElementsByClassName("AgentAutocomplete");

    for (let i = 0; i < hashtagInputs.length; i++) {
        autocomplete(hashtagInputs[i], hashtags);
    }
    for (let i = 0; i < departmentInputs.length; i++) {
        autocomplete(departmentInputs[i], departments);
    }
    for (let i = 0; i < faqInputs.length; i++) {
        autocomplete(faqInputs[i], faqs);
    }
    for (let i = 0; i < agentInputs.length; i++) {
        autocomplete(agentInputs[i], agents);
    }
}

addAutocomplete();


//Make input form adjust to input's length

function adjustInput() {
    const input = document.querySelectorAll('.UserInfo, .DepartmentTag'); // get the input element

    input.forEach(function (elem) {
        elem.addEventListener('input', resizeInput); // bind the "resizeInput" callback on "input" event
        resizeInput.call(elem); // immediately call the function
    });

    function resizeInput() {
        if (this.value) {
            this.style.width = this.value.length + 10 + "ch";
        }
    }
}

adjustInput();

//Click + button on fields opens a new input, x removes them

function removableDepartmentFields() {
    let addButton = document.querySelector("#NewDepartment");
    let departmentForm = document.querySelector("#DepartmentForm, #DepartmentsList");
    let submitButton = document.querySelector("#SubmitDepartments, #SubmitEdit");

    let removeButtons = document.querySelectorAll(".RemoveDepartment");
    for (let i = 0; i < removeButtons.length; i++) {
        removeButtons[i].addEventListener("click", function () {
            let departmentFieldParent = removeButtons[i].parentNode.parentNode;
            departmentFieldParent.removeChild(removeButtons[i].parentNode);
            submitButton.style.display = "inline-flex";
        });
    }

    if (submitButton) submitButton.style.display = "none";

    if (addButton) {
        addButton.addEventListener("click", function () {
            let newInput = document.createElement("input");
            newInput.setAttribute("class", "Tag DepartmentTag DepartmentAutocomplete");
            newInput.setAttribute("type", "text");
            newInput.setAttribute("name", "departments[]");
            let newDiv = document.createElement("div");
            newDiv.setAttribute("class", "DepartmentField");
            let newRemoveIcon = document.createElement("i");
            newRemoveIcon.setAttribute("class", "fa fa-xmark fa-lg RemoveDepartment");
            newRemoveIcon.style.display = "inline-flex";

            newRemoveIcon.addEventListener("click", function () {
                let departmentFieldParent = newRemoveIcon.parentNode.parentNode;
                departmentFieldParent.removeChild(newRemoveIcon.parentNode);
                submitButton.style.display = "inline-flex";
            });

            newDiv.appendChild(newInput);
            newDiv.appendChild(newRemoveIcon);

            if (submitButton.id == "SubmitDepartments") { departmentForm.removeChild(submitButton); }
            departmentForm.appendChild(newDiv);
            if (submitButton.id == "SubmitDepartments") { departmentForm.appendChild(submitButton); }

            submitButton.style.display = "inline-flex";
            adjustInput();
            addAutocomplete();
        });
    }
}

removableDepartmentFields();

//Click + button on hashtag fields opens a new input

let addButton = document.querySelector("#AddHashtag");
let hashtagDiv = document.querySelector("#HashtagInput div:first-of-type");
if (addButton) {

    addButton.addEventListener("click", function () {
        let newField = document.createElement("div");
        newField.setAttribute("class", "HashtagField");
        let newDiv = document.createElement("div");
        newDiv.setAttribute("class", "InputIcon");
        let newIcon = document.createElement("i");
        newIcon.innerHTML = "#";
        let newInput = document.createElement("input");
        newInput.setAttribute("class", "HashtagsAutocomplete");
        newInput.setAttribute("type", "text");
        newInput.setAttribute("name", "hashtags[]");
        let newRemoveIcon = document.createElement("i");
        newRemoveIcon.setAttribute("class", "fa fa-xmark fa-lg RemoveHashtag");

        newRemoveIcon.addEventListener("click", function () {
            let hashtagInput = newRemoveIcon.parentNode.parentNode;
            hashtagInput.removeChild(newRemoveIcon.parentNode);
        });

        hashtagDiv.appendChild(newField);
        newField.appendChild(newDiv);
        newDiv.appendChild(newIcon);
        newDiv.appendChild(newInput);
        newField.append(newRemoveIcon);

        addAutocomplete();
    });
}
let removeIcons = document.querySelectorAll(".RemoveHashtag");
for (let i = 0; i < removeIcons.length; i++) {
    removeIcons[i].addEventListener("click", function () {
        let hashtagInput = removeIcons[i].parentNode.parentNode;
        hashtagInput.removeChild(removeIcons[i].parentNode);
    });
}

addAutocomplete();


