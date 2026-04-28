/**
 * Обёртка над noUiSlider для умного фильтра: синхронизация с инпутами и smartFilter.keyup().
 * Совместимо с вызовами trackBar.setMinFilteredValue / setMaxFilteredValue после ajax.
 */
window.FxSmartFilterNuRange = function(arParams)
{
	var minInput = BX(arParams.minInputId);
	var maxInput = BX(arParams.maxInputId);
	var el = document.getElementById(arParams.nouiId);
	if (!el || typeof noUiSlider === 'undefined' || !minInput || !maxInput)
	{
		return;
	}

	if (typeof el.noUiSlider !== 'undefined')
	{
		el.noUiSlider.destroy();
	}

	this.minInput = minInput;
	this.maxInput = maxInput;
	this.el = el;
	this.precision = arParams.precision != null ? parseInt(arParams.precision, 10) : 0;

	var minPrice = parseFloat(arParams.minPrice);
	var maxPrice = parseFloat(arParams.maxPrice);
	var vMin = parseFloat(minInput.value);
	var vMax = parseFloat(maxInput.value);
	if (isNaN(vMin)) vMin = minPrice;
	if (isNaN(vMax)) vMax = maxPrice;
	vMin = Math.max(minPrice, Math.min(maxPrice, vMin));
	vMax = Math.max(minPrice, Math.min(maxPrice, vMax));
	if (vMin > vMax)
	{
		var t = vMin;
		vMin = vMax;
		vMax = t;
	}

	var step = this.precision > 0 ? Math.pow(0.1, this.precision) : 1;

	var self = this;
	noUiSlider.create(el, {
		start: [vMin, vMax],
		connect: true,
		behaviour: 'tap-drag',
		range: {
			min: minPrice,
			max: maxPrice
		},
		step: step,
		format: {
			to: function(value)
			{
				value = parseFloat(value);
				if (self.precision > 0)
				{
					return parseFloat(value.toFixed(self.precision));
				}
				return Math.round(value);
			},
			from: function(value)
			{
				return parseFloat(value);
			}
		}
	});

	this.slider = el.noUiSlider;

	this.slider.on('change', function(values)
	{
		self.writeInputs(values);
	});

	BX.bind(this.minInput, 'change', BX.proxy(this.onInputChange, this));
	BX.bind(this.maxInput, 'change', BX.proxy(this.onInputChange, this));
};

window.FxSmartFilterNuRange.prototype.writeInputs = function(values)
{
	var s0 = this.formatOut(values[0]);
	var s1 = this.formatOut(values[1]);
	this.minInput.value = s0;
	this.maxInput.value = s1;
	if (window.smartFilter)
	{
		window.smartFilter.keyup(this.minInput);
	}
};

window.FxSmartFilterNuRange.prototype.formatOut = function(v)
{
	v = parseFloat(v);
	if (isNaN(v)) return '';
	if (this.precision > 0)
	{
		return v.toFixed(this.precision);
	}
	return String(Math.round(v));
};

window.FxSmartFilterNuRange.prototype.onInputChange = function()
{
	var a = parseFloat(this.minInput.value);
	var b = parseFloat(this.maxInput.value);
	if (isNaN(a) || isNaN(b))
	{
		return;
	}
	this.slider.set([a, b]);
};

/** Цветовые полосы фасета Bitrix — оставляем заглушки, чтобы не ломался ajax-обновлятор. */
window.FxSmartFilterNuRange.prototype.setMinFilteredValue = function() {};
window.FxSmartFilterNuRange.prototype.setMaxFilteredValue = function() {};
