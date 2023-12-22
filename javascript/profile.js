
"use strict";

//Make edit forms and submit button disabled until edit button is clicked

const formInput = document.querySelectorAll('.UserInfo');
const formSubmit = document.querySelector('#SubmitEdit');
const editButton = document.querySelector('.EditButton');
const errorMessage = document.querySelector('.ErrorMessage');
const removeDepartments = document.querySelectorAll(".RemoveDepartment");
const addDepartments = document.querySelector("#NewDepartment");

if (formSubmit) formSubmit.style.display = "none";
if (addDepartments) addDepartments.style.display = "none";
if (removeDepartments) removeDepartments.forEach(function (elem) {
    elem.style.display = "none";
});
if (formInput) formInput.forEach(function (elem) {
    elem.readOnly = true;
});

if (editButton) editButton.addEventListener('click', enableInputForms);

function enableInputForms() {
    formInput.forEach(function (elem) {
        elem.readOnly = false;
    });
    formSubmit.style.display = "inline-flex";
    addDepartments.style.display = "inline-flex";
    removeDepartments.forEach(function (elem) {
        elem.style.display = "inline-flex";
    });
    if (errorMessage) {
        errorMessage.classList.add('FadeOut');
    }
}

//Hide password fields until Changed Password button is clicked


const passwordFields = document.querySelectorAll('.PasswordField');
const passwordInputs = document.querySelectorAll('.PasswordInput');
const editPasswordButton = document.querySelector('#AlterPasswordButton');

passwordFields.forEach(function (elem) {
    elem.style.display = "none";
});

if (editPasswordButton) editPasswordButton.addEventListener('click', enablePasswordForms);

function enablePasswordForms() {
    passwordFields.forEach(function (elem) {
        elem.style.display = "flex";
    });
    passwordInputs.forEach(function (elem) {
        elem.readOnly = false;
    });
    editPasswordButton.style.display = "none";
    formSubmit.style.display = "inline-flex";
}

