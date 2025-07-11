
window.onload = function (e) {
    stepsprogress();
};

function stepsprogress() {
    const prevBtns = document.querySelectorAll(".btn-prev");
    const nextBtns = document.querySelectorAll(".btn-next"); 
    const formSteps = document.querySelectorAll(".form-step");
    const progressSteps = document.querySelectorAll(".progress-step");

    let formStepsNum = 0;

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", (event) => {

            event.preventDefault();

            formStepsNum++;
            updateFormSteps();
            updateProgressbar();
        });
    });

    prevBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            formStepsNum--;
            updateFormSteps();
            updateProgressbar();
        });
    });

    function updateFormSteps() {
        formSteps.forEach((formStep) => {
            formStep.classList.contains("form-step-active") &&
                formStep.classList.remove("form-step-active");
        });

        formSteps[formStepsNum].classList.add("form-step-active");
    }

    function updateProgressbar() {
        progressSteps.forEach((progressStep, idx) => {
            if (idx < formStepsNum + 1) {
                progressStep.classList.add("active");
            } else {
                progressStep.classList.remove("active");
            }
        });
    }
}

function validateInputs(ths) {
    let inputsValid = true;

    const inputs =
        document.querySelectorAll("input");
    for (let i = 0; i < inputs.length; i++) {
        const valid = inputs[i].checkValidity();
        if (!valid) {
            inputsValid = false;
            inputs[i].classList.add("invalid-input");
        } else {
            inputs[i].classList.remove("invalid-input");
        }
    }
    return inputsValid;
}