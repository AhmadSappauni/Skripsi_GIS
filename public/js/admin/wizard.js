// public/js/admin/wizard.js

let step = 0;

const steps = document.querySelectorAll('.wizard-content');
const indicators = document.querySelectorAll('.step');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');
const submitBtn = document.getElementById('submitBtn');

const latitude = document.getElementById('latitude');
const longitude = document.getElementById('longitude');

function isStepValid(stepIndex) {
    if (stepIndex === 0) {
        const nama = document.getElementById('namaTempat').value;
        const kategori = document.querySelector('input[name="kategori"]:checked');
        return nama.trim() !== '' && kategori;
    }

    if (stepIndex === 2) {
        return latitude.value && longitude.value && jamOperasional.value;
    }

    return true;
}

function showStep(i) {
    steps.forEach(s => s.classList.remove('active'));
    indicators.forEach(s => s.classList.remove('active'));

    steps[i].classList.add('active');
    indicators[i].classList.add('active');

    prevBtn.style.display = i === 0 ? 'none' : 'inline-flex';

    if (i === steps.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }

    document.getElementById('progressBar').style.width =
        ((i + 1) / steps.length * 100) + '%';

    // FIX MAP RENDER
    if (i === 2 && window.map) {
        setTimeout(() => {
            map.invalidateSize();
            map.setView(
                [latitude.value || -2.5489, longitude.value || 118.0149],
                6
            );
        }, 300);
    }
}

nextBtn.onclick = () => {
    if (!isStepValid(step)) return;
    step++;
    showStep(step);
    if (step === steps.length - 1) updatePreview();
};

prevBtn.onclick = () => {
    step--;
    showStep(step);
};

showStep(step);
