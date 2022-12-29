require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import html2canvas from 'html2canvas';
window.html2canvas = html2canvas;