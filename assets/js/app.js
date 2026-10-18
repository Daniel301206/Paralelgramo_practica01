document.addEventListener('DOMContentLoaded', function () {
    const baseInput = document.getElementById('txtBase');
    const ladoInput = document.getElementById('txtLado');
    const alturaInput = document.getElementById('txtAltura');
    const areaOutput = document.getElementById('txtA');
    const perimetroOutput = document.getElementById('txtP');

    if (!baseInput || !ladoInput || !alturaInput || !areaOutput || !perimetroOutput) {
        return;
    }

    function calcular() {
        const base = Number(baseInput.value);
        const lado = Number(ladoInput.value);
        const altura = Number(alturaInput.value);
        const area = base * altura;
        const perimetro = 2 * (base + lado);

        if (base > 0 && lado > 0 && altura > 0
            && Number.isFinite(area) && Number.isFinite(perimetro)) {
            areaOutput.value = area.toLocaleString('es', {
                maximumFractionDigits: 2
            });
            perimetroOutput.value = perimetro.toLocaleString('es', {
                maximumFractionDigits: 2
            });
            return true;
        }

        areaOutput.value = '';
        perimetroOutput.value = '';
        return false;
    }

    [baseInput, ladoInput, alturaInput].forEach(function (input) {
        input.addEventListener('input', calcular);
    });

    calcular();
});
