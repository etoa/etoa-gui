import jQuery from 'jquery'
window.$ = window.jQuery = jQuery

require('./jquery.cookie.js')

import generatePlanetName from './planetname'
window.generatePlanetName = generatePlanetName

import './tutorial.js'

import { DateTime } from "luxon";
window.DateTime = DateTime;
