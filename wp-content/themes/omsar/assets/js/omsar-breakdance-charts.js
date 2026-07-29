/**
 * Initialize OMSAR charts inside the Breakdance builder (SSR inline scripts do not run there).
 *
 * @package OMSAR
 */
(function () {
	'use strict';

	function parseConfig(wrapper) {
		var raw = wrapper.getAttribute('data-omsar-chart-config');
		if (!raw) {
			return null;
		}

		try {
			return JSON.parse(raw);
		} catch (error) {
			return null;
		}
	}

	function getCanvas(wrapper) {
		return wrapper.querySelector('canvas');
	}

	function destroyExistingChart(canvas) {
		if (typeof Chart === 'undefined' || !canvas) {
			return;
		}

		var existing = Chart.getChart(canvas);
		if (existing) {
			existing.destroy();
		}
	}

	function buildYAxisTicks(config, scale, useDatasets) {
		scale.ticks = [];

		if (!config.yAxisNumericValues || !config.yAxisNumericValues.length) {
			return;
		}

		var allNumeric = config.yAxisNumericValues.every(function (val) {
			return val !== null && !isNaN(val);
		});

		if (allNumeric) {
			scale.min = Math.min.apply(null, config.yAxisNumericValues);
			scale.max = Math.max.apply(null, config.yAxisNumericValues);

			config.yAxisNumericValues.forEach(function (numVal, index) {
				var baseLabel = config.yAxisLabels[index] || String(numVal);
				var displayLabel = baseLabel;

				if (config.yAxisLabel && String(config.yAxisLabel).trim() !== '') {
					displayLabel = baseLabel + config.yAxisLabel;
				}

				scale.ticks.push({
					value: numVal,
					label: displayLabel
				});
			});
			return;
		}

		var maxDataValue = 0;

		if (useDatasets) {
			config.datasets.forEach(function (dataset) {
				if (dataset.data && dataset.data.length) {
					var datasetMax = Math.max.apply(null, dataset.data);
					if (datasetMax > maxDataValue) {
						maxDataValue = datasetMax;
					}
				}
			});
		} else if (config.data && config.data.length) {
			maxDataValue = Math.max.apply(null, config.data);
		}

		scale.max = maxDataValue * 1.1;

		config.yAxisLabels.forEach(function (label, index) {
			var numVal = config.yAxisNumericValues[index];
			var displayLabel = label;

			if (config.yAxisLabel && String(config.yAxisLabel).trim() !== '') {
				displayLabel = label + config.yAxisLabel;
			}

			if (numVal !== null && !isNaN(numVal)) {
				scale.ticks.push({
					value: numVal,
					label: displayLabel
				});
			} else {
				scale.ticks.push({
					value: (index / (config.yAxisLabels.length - 1)) * scale.max,
					label: displayLabel
				});
			}
		});
	}

	function yAxisTickCallback(value, index, ticks) {
		var tick = ticks.find(function (item) {
			return item.value === value;
		});

		return tick && tick.label ? tick.label : value;
	}

	function initBarChart(wrapper, config, canvas, context) {
		var isModal = context === 'modal';

		new Chart(canvas.getContext('2d'), {
			type: 'bar',
			data: {
				labels: config.labels,
				datasets: config.datasets
			},
			options: {
				responsive: true,
				maintainAspectRatio: isModal,
				plugins: {
					legend: {
						display: config.showLegend,
						position: config.legendPosition,
						align: config.legendAlignment,
						labels: {
							boxWidth: 10,
							boxHeight: 10,
							color: config.legendTextColor || '#333333'
						}
					},
					tooltip: {
						enabled: true,
						callbacks: {
							label: function (context) {
								return context.dataset.label + ': ' + context.parsed.y + (config.yAxisLabel ? ' ' + String(config.yAxisLabel).replace(/[()]/g, '') : '');
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						position: config.reverseChart ? 'right' : 'left',
						afterBuildTicks: function (scale) {
							buildYAxisTicks(config, scale, true);
						},
						ticks: {
							callback: yAxisTickCallback
						},
						title: {
							display: !!(config.showYAxisLabelTitle && config.yAxisLabel),
							text: config.yAxisLabel || ''
						},
						grid: {
							color: 'rgba(0, 0, 0, 0.1)'
						}
					},
					x: {
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	function initLineChart(wrapper, config, canvas, context) {
		new Chart(canvas.getContext('2d'), {
			type: 'line',
			data: {
				labels: config.labels,
				datasets: [{
					label: config.lineLabel,
					data: config.data,
					borderColor: config.lineColor,
					backgroundColor: config.fillArea ? config.fillColor : 'transparent',
					borderWidth: config.lineWidth,
					pointRadius: config.pointRadius,
					pointBackgroundColor: config.pointColor,
					pointBorderColor: config.pointColor,
					pointHoverRadius: config.pointRadius > 0 ? config.pointRadius + 2 : 0,
					fill: config.fillArea,
					tension: 0.1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: config.showLegend,
						position: config.legendPosition,
						align: config.legendAlignment,
						labels: {
							boxWidth: 10,
							boxHeight: 10,
							color: config.legendTextColor || '#333333'
						}
					},
					tooltip: {
						enabled: true,
						callbacks: {
							label: function (context) {
								return context.dataset.label + ': ' + context.parsed.y;
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						position: config.reverseChart ? 'right' : 'left',
						afterBuildTicks: function (scale) {
							buildYAxisTicks(config, scale, false);
						},
						ticks: {
							callback: yAxisTickCallback
						},
						title: {
							display: !!config.yAxisLabel,
							text: config.yAxisLabel
						},
						grid: {
							display: config.showGrid,
							color: config.gridColor,
							lineWidth: config.gridLineWidth
						}
					},
					x: {
						grid: {
							display: config.showGrid,
							color: config.gridColor,
							lineWidth: config.gridLineWidth
						}
					}
				}
			}
		});
	}

	function initCircularChart(wrapper, config, canvas) {
		new Chart(canvas.getContext('2d'), {
			type: config.type,
			data: {
				labels: config.labels,
				datasets: [{
					data: config.data,
					backgroundColor: config.backgroundColor,
					borderColor: config.borderColor,
					borderWidth: config.borderWidth
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: config.showLegend,
						position: config.legendPosition,
						align: config.legendAlignment,
						labels: {
							boxWidth: 10,
							boxHeight: 10,
							color: config.legendTextColor || '#333333'
						}
					},
					tooltip: {
						enabled: true,
						callbacks: {
							label: function (context) {
								var value = context.parsed || 0;
								var total = context.dataset.data.reduce(function (a, b) {
									return a + b;
								}, 0);
								var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
								return value + ' (' + percentage + '%)';
							}
						}
					}
				},
				cutout: config.cutout || 0
			}
		});
	}

	function initChartWrapper(wrapper) {
		var type = wrapper.getAttribute('data-omsar-chart-type');
		var config = parseConfig(wrapper);
		var canvas = getCanvas(wrapper);

		if (!type || !config || !canvas) {
			return;
		}

		var signature = type + ':' + wrapper.getAttribute('data-omsar-chart-config');
		if (wrapper.getAttribute('data-omsar-chart-signature') === signature && typeof Chart !== 'undefined' && Chart.getChart(canvas)) {
			return;
		}

		if (typeof Chart === 'undefined') {
			window.setTimeout(function () {
				initChartWrapper(wrapper);
			}, 100);
			return;
		}

		destroyExistingChart(canvas);

		if (type === 'bar') {
			initBarChart(wrapper, config, canvas);
		} else if (type === 'line') {
			initLineChart(wrapper, config, canvas);
		} else if (type === 'circular') {
			initCircularChart(wrapper, config, canvas);
		}

		wrapper.setAttribute('data-omsar-chart-signature', signature);
	}

	var EXPORT_BUTTON_SELECTOR = '.omsar-chart-export-icon, .omsar-line-chart-export-icon, .omsar-circular-chart-export-icon';
	var ZOOM_BUTTON_SELECTOR = '.omsar-chart-zoom-icon, .omsar-line-chart-zoom-icon, .omsar-circular-chart-zoom-icon';

	function getChartUi(type) {
		if (type === 'line') {
			return {
				modalIdPrefix: 'omsar-line-chart-modal-',
				modalClass: 'omsar-line-chart-modal',
				modalContentClass: 'omsar-line-chart-modal-content',
				modalCloseClass: 'omsar-line-chart-modal-close',
				modalTitleClass: 'omsar-line-chart-modal-title',
				modalContainerClass: 'omsar-line-chart-modal-container'
			};
		}

		if (type === 'circular') {
			return {
				modalIdPrefix: 'omsar-circular-chart-modal-',
				modalClass: 'omsar-circular-chart-modal',
				modalContentClass: 'omsar-circular-chart-modal-content',
				modalCloseClass: 'omsar-circular-chart-modal-close',
				modalTitleClass: 'omsar-circular-chart-modal-title',
				modalContainerClass: 'omsar-circular-chart-modal-container'
			};
		}

		return {
			modalIdPrefix: 'omsar-chart-modal-',
			modalClass: 'omsar-chart-modal',
			modalContentClass: 'omsar-chart-modal-content',
			modalCloseClass: 'omsar-chart-modal-close',
			modalTitleClass: 'omsar-chart-modal-title',
			modalContainerClass: 'omsar-chart-modal-container'
		};
	}

	function getCanvasFromButton(button, wrapper) {
		var canvasId = button.getAttribute('data-canvas-id');
		if (canvasId) {
			return document.getElementById(canvasId);
		}

		return wrapper ? wrapper.querySelector('canvas') : null;
	}

	function handleExportClick(button) {
		var wrapper = button.closest('[data-omsar-chart-type]');
		var config = wrapper ? parseConfig(wrapper) : null;
		var canvas = getCanvasFromButton(button, wrapper);

		if (!canvas) {
			return;
		}

		var exportCanvas = document.createElement('canvas');
		exportCanvas.width = canvas.width;
		exportCanvas.height = canvas.height;
		var exportCtx = exportCanvas.getContext('2d');
		var bgColor = config && config.containerBackground ? config.containerBackground : '#ffffff';

		exportCtx.fillStyle = bgColor;
		exportCtx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
		exportCtx.drawImage(canvas, 0, 0);

		var chartTitle = button.getAttribute('data-chart-title') || 'chart';
		var link = document.createElement('a');
		link.download = chartTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.png';
		link.href = exportCanvas.toDataURL('image/png');
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
	}

	function closeModal(modal, handleEsc) {
		modal.classList.remove('active');
		window.setTimeout(function () {
			modal.remove();
		}, 300);

		if (handleEsc) {
			document.removeEventListener('keydown', handleEsc);
		}
	}

	function handleZoomClick(button) {
		var wrapper = button.closest('[data-omsar-chart-type]');
		if (!wrapper || typeof Chart === 'undefined') {
			return;
		}

		var type = wrapper.getAttribute('data-omsar-chart-type');
		var config = parseConfig(wrapper);
		var canvas = getCanvasFromButton(button, wrapper);
		var widgetId = button.getAttribute('data-widget-id');
		var chartTitle = button.getAttribute('data-chart-title') || '';

		if (!type || !config || !canvas || !widgetId) {
			return;
		}

		var ui = getChartUi(type);
		var modalId = ui.modalIdPrefix + widgetId;
		var existingModal = document.getElementById(modalId);

		if (existingModal) {
			existingModal.remove();
		}

		var modal = document.createElement('div');
		modal.id = modalId;
		modal.className = ui.modalClass + ' active';
		modal.setAttribute('role', 'dialog');
		modal.setAttribute('aria-modal', 'true');
		modal.setAttribute('aria-labelledby', modalId + '-title');

		var modalContent = document.createElement('div');
		modalContent.className = ui.modalContentClass + ' container';

		var closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.className = ui.modalCloseClass;
		closeButton.setAttribute('aria-label', 'Close modal');
		closeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';

		var modalContainer = document.createElement('div');
		modalContainer.className = ui.modalContainerClass;

		var clonedCanvas = document.createElement('canvas');
		modalContainer.appendChild(clonedCanvas);

		if (chartTitle) {
			var modalTitle = document.createElement('h3');
			modalTitle.id = modalId + '-title';
			modalTitle.className = ui.modalTitleClass;
			modalTitle.textContent = chartTitle;
			modalContent.appendChild(modalTitle);
		}

		modalContent.appendChild(closeButton);
		modalContent.appendChild(modalContainer);
		modal.appendChild(modalContent);
		document.body.appendChild(modal);

		var handleEsc = function (event) {
			if (event.key === 'Escape') {
				closeModal(modal, handleEsc);
			}
		};

		closeButton.addEventListener('click', function () {
			closeModal(modal, handleEsc);
		});

		document.addEventListener('keydown', handleEsc);

		modal.addEventListener('click', function (event) {
			if (event.target === modal) {
				closeModal(modal, handleEsc);
			}
		});

		window.setTimeout(function () {
			destroyExistingChart(clonedCanvas);

			if (type === 'bar') {
				initBarChart(wrapper, config, clonedCanvas, 'modal');
			} else if (type === 'line') {
				initLineChart(wrapper, config, clonedCanvas, 'modal');
			} else if (type === 'circular') {
				initCircularChart(wrapper, config, clonedCanvas, 'modal');
			}
		}, 100);
	}

	function bindChartActions() {
		if (document.documentElement.getAttribute('data-omsar-chart-actions-bound')) {
			return;
		}

		document.documentElement.setAttribute('data-omsar-chart-actions-bound', '1');

		document.addEventListener('click', function (event) {
			var exportButton = event.target.closest(EXPORT_BUTTON_SELECTOR);
			if (exportButton) {
				event.preventDefault();
				handleExportClick(exportButton);
				return;
			}

			var zoomButton = event.target.closest(ZOOM_BUTTON_SELECTOR);
			if (zoomButton) {
				event.preventDefault();
				handleZoomClick(zoomButton);
			}
		});
	}

	function scanCharts(root) {
		var scope = root || document;
		var wrappers = scope.querySelectorAll('[data-omsar-chart-type][data-omsar-chart-config]');

		wrappers.forEach(function (wrapper) {
			initChartWrapper(wrapper);
		});
	}

	window.omsarInitBreakdanceCharts = scanCharts;

	bindChartActions();

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			scanCharts(document);
		});
	} else {
		scanCharts(document);
	}

	if (typeof MutationObserver !== 'undefined' && document.body) {
		var scanTimer = null;
		var observer = new MutationObserver(function () {
			if (scanTimer) {
				window.clearTimeout(scanTimer);
			}
			scanTimer = window.setTimeout(function () {
				scanCharts(document);
			}, 120);
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['data-omsar-chart-config', 'data-omsar-chart-type']
		});
	}
})();
