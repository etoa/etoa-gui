/*
   ETOA Design Rediscovery by HeaX

  Stand: 28. August 21
  Version: 1.0
*/

/* Eigene Scripte */

const REFERENCE_WIDTH = 1680;
const REFERENCE_Height = 1440;
const REFERENCE_FONT_SIZE = 16;

let dpiScale = sessionStorage.getItem("devicePixelRatio");
if (dpiScale == null) {
    dpiScale = window.devicePixelRatio;
    sessionStorage.setItem("devicePixelRatio", dpiScale);
}

const DPI_SCALE = dpiScale;

class App {
    element;

    mainMenu;
    topMenu;
    planetDropdownMobile;
    mobileReloader;

    gameArea;
    isMobile;
    isTablet;
    planetSelectionMobile;
    planetSelectionMobileAttachPoint;
    planetSelectionMobileAttached = false;
    mobileReloaderVisible = false;
    reloadTriggered = false;

    // only used on mobile
    scrollStartPosition;

    inputMode = "mouse";

    resolutionPostProcessors;

    mount(element) {
        this.element = element;
        this.gameArea = element.querySelector("#game-area");
        this.resolutionPostProcessors = [];
        element.classList.add("mouse");
        document.body.addEventListener("touchstart", () => this.onTouchStart());
        window.addEventListener("scroll", () => this.onScroll());
        window.addEventListener("resize", () => this.onWindowResize());
        this.replacePixelSizes();
        this.mountMainNavigation();
        this.mountMobileNavigation();
        this.mountResourceBar();
        this.mountInfoBox();
        this.mountPlanetCircle();
        this.mountBuildOverview();
        this.mountShipyard();
        this.mountArmory();
        this.mountSectorMap();
        this.resolutionPostProcessors.forEach(p => p());
    }


    mountMainNavigation() {
        const planetDropdownToggle = document.querySelector(
            "#toggle-planet-dropdown"
        );
        const planetDropdown = document.querySelector("#planet-dropdown");
        const serverTime = document.querySelector("#server-time");
        planetDropdownToggle.addEventListener("click", () => {
            planetDropdown.setAttribute(
                "data-expanded",
                planetDropdown.getAttribute("data-expanded") !== "true"
                    ? "true"
                    : "false"
            );
        });
        this.mountServerTime(serverTime);
    }

    mountMobileNavigation() {
        const mainMenuToggle = document.querySelector("#main-menu-toggle");
        const topMenuToggle = document.querySelector("#top-menu-toggle");
        const mainMenu = document.querySelector(".navigation-main");
        const topMenu = document.querySelector(".navigation-top");
        const mobileReloader = document.querySelector(".mobile-reloader");
        const serverTime = document.querySelector("#server-time-mobile");
        const planetNameMobile = document.querySelector(
            "#current-planet-name-mobile"
        );
        const planetDropdownMobile = document.querySelector(
            "#planet-dropdown-mobile"
        );
        const planetSelectionMobile = document.querySelector(
            "#mobile-planet-selection"
        );
        mainMenuToggle.addEventListener("click", () => this.toggleMainMenu());
        topMenuToggle.addEventListener("click", () => this.toggleTopMenu());
        planetNameMobile.addEventListener("click", () =>
            this.togglePlanetDropdownMobile()
        );
        mainMenu.addEventListener("click", (event) => event.stopPropagation());
        topMenu.addEventListener("click", (event) => event.stopPropagation());
        planetDropdownMobile.addEventListener("click", (event) =>
            event.stopPropagation()
        );

        this.mainMenu = mainMenu;
        this.topMenu = topMenu;
        this.mobileReloader = mobileReloader;
        this.planetDropdownMobile = planetDropdownMobile;
        this.planetSelectionMobile = planetSelectionMobile;
        this.planetSelectionMobileAttachPoint = planetSelectionMobile.offsetTop;
        this.mountServerTime(serverTime);
        this.onScroll();
    }

    mountServerTime(element) {
        const timeParts = element.innerText.split(":");
        if (timeParts.length < 3) {
            console.warn("Failed to mount server time on element " + element);
            return;
        }
        const hour = parseInt(timeParts[0]);
        const minute = parseInt(timeParts[1]);
        const second = parseInt(timeParts[2]);

        const startTimestamp = Date.now();
        const startDate = new Date(startTimestamp);
        const referenceDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDay(), hour, minute, second);
        if (referenceDate.getTime() > startTimestamp) {
            referenceDate.setDate(referenceDate.getTime() - 24 * 3600);
        }
        const referenceTimestamp = referenceDate.getTime();

        const tickServertime = () => {
            if (!element) {
                return;
            }
            const now = new Date(referenceTimestamp + Date.now() - startTimestamp);
            element.innerText = now.toLocaleTimeString('de-DE');
            setTimeout(() => {
                tickServertime();
            }, 500);
        };

        tickServertime();
    }

    mountResourceBar() {
        const caption = [...document.querySelectorAll("caption")].find(
            (c) => c.innerText === "Ressourcen"
        );
        if (caption == null) {
            return;
        }
        const table = caption.parentNode;
        table.classList.add("resBoxTable");
    }

    /**
     * Adds a collapse functionality to the main info box on the overview
     */
    mountInfoBox() {
        const infoBox = document.querySelector(".overviewInfoTextContainer");
        if (infoBox == null) {
            return;
        }

        const title = infoBox.querySelector(".infoboxtitle");
        const content = infoBox.querySelector(".infoboxcontent");
        const collapseToggle = document.createElement("button");
        collapseToggle.classList.add("collapse-toggle");
        title.addEventListener("click", () => {
            const collapse = infoBox.getAttribute("data-collapsed") !== "true";
            infoBox.setAttribute("data-collapsed", collapse ? "true" : "false");
            sessionStorage.setItem("overviewInfoToggle", collapse ? "true" : "false");
            if (collapse) {
                sessionStorage.setItem("overviewInfoText", content.innerHTML);
            }
        });
        title.appendChild(collapseToggle);
        if (sessionStorage.getItem("overviewInfoText") === content.innerHTML) {
            infoBox.setAttribute("data-collapsed", sessionStorage.getItem("overviewInfoToggle") === "true" ? "true" : "false");
        }
    }

    mountPlanetCircle() {
        const planetCircle = document.getElementById(
            "planet_circle_inner_container"
        );
        if (planetCircle == null) {
            return;
        }
        const planetCircleArea = planetCircle.parentNode;
        planetCircleArea.classList.add("planet_circle_outer_container");
        planetCircleArea.style.width = null;
        planetCircleArea.style.height = null;
        planetCircleArea.style.padding = null;
        planetCircleArea.style.margin = null;

        planetCircle.style.width = null;
        planetCircle.style.height = null;

        let activePlanet = null;

        for (let image of planetCircle.querySelectorAll("img")) {
            const currentPlanet = image.parentNode.parentNode;
            image.removeAttribute("width");
            image.removeAttribute("height");
            image.classList.add("planetImage");
            image.addEventListener("mouseover", () => {
                if (activePlanet != null) {
                    activePlanet.classList.remove("activePlanet");
                }
                activePlanet = currentPlanet;
                currentPlanet.classList.add("activePlanet");
            });
        }

        const circleElements = [...planetCircle.childNodes].filter(
            (n) => n.nodeName.toLowerCase() === "div"
        );

        const planets = [];
        const labels = [];
        for (let i = 0; i < circleElements.length; i++) {
            if (i % 2 === 0) {
                planets.push(circleElements[i]);
            } else {
                labels.push(circleElements[i]);
            }
        }

        for (let i = 0; i < planets.length; i++) {
            const planet = planets[i];
            const label = labels[i];
            planet.classList.add("planet");
            planet.classList.add("planet-" + (i + 1));
            label.classList.add("planetLabel");
            planet.style.left =
                planet.style.top =
                    planet.style.width =
                        planet.style.height =
                            label.style.left =
                                label.style.top =
                                    label.style.width =
                                        null;
            planet.appendChild(label);
        }

        const center = planetCircle.querySelector("table");
        const centerBody = center.querySelector("tbody");
        centerBody.querySelector("tr").remove();
        center.removeAttribute("width");
        center.style.width = "50%";
        center.style.top = "50%";
        center.style.left = "50%";
        center.style.position = "absolute";
        center.style.transform = "translate(-50%,-50%)";

        while (
            planetCircleArea.previousSibling != null &&
            planetCircleArea.previousSibling.nodeName === "BR"
            ) {
            planetCircleArea.previousSibling.remove();
        }
    }

    mountBuildOverview() {
        const researchTitles = this.element.querySelectorAll(
            ".buildOverviewObjectTitle"
        );
        if (researchTitles == null || researchTitles.length === 0) {
            return;
        }
        for (let title of researchTitles) {
            const tile = title.parentNode;
            const cell = tile.parentNode;
            const anchor = title.nextElementSibling;
            cell.style.width = cell.style.height = cell.style.padding = null;
            tile.style.width = tile.style.height = tile.style.padding = null;
            anchor.style.width = anchor.style.height = null;
            cell.classList.add("buildOverviewObject");
        }
        for (let element of this.element.querySelectorAll(".buildOverviewObjectNone")) {
            element.style.width = element.style.height = element.style.padding = null;
        }
        const table = researchTitles[0].parentNode.parentNode.parentNode.parentNode.parentNode;
        table.style.borderSpacing = 0;
    }

    mountShipyard() {
        const forms = document.querySelectorAll("form[action='?page=shipyard']");
        if (forms.length < 1) {
            return;
        }
        const shipyard = [...forms].find((f) => f.innerHTML.indexOf("Schiff") >= 0);
        const categories = shipyard.querySelectorAll(".shipCategory");
        let compactMode = false;
        for (let category of categories) {
            const rows = category.querySelectorAll("tr");
            if (rows.length > 0 && rows[0].childNodes.length > 5) {
                // compact view detected, no mobile adjustments available
                compactMode = true;
                break;
            }
            for (let i = 0; i < rows.length; i += 7) {
                const scheduleRow = rows[i + 3];
                scheduleRow.classList.add("shipRowSchedule");
            }
        }
        const images = shipyard.querySelectorAll("img");
        for (let image of images) {
            image.classList.add(compactMode ? "shipImageSmall" : "shipImage");
            image.removeAttribute("width");
            image.removeAttribute("height");
            if (image.parentNode.nodeName === "A") {
                const anchor = image.parentNode;
                const cell = anchor.parentNode;
                anchor.style.display = "block";
                cell.removeAttribute("width");
                cell.removeAttribute("height");
                cell.style.verticalAlign = "top";
            } else if (image.parentNode.nodeName === "TD") {
                const cell = image.parentNode;
                image.style.display = "block";
                cell.removeAttribute("width");
                cell.removeAttribute("height");
                cell.style.verticalAlign = "top";
            }
        }
    }

    mountArmory() {
        const forms = document.querySelectorAll("form[action='?page=defense']");
        if (forms.length < 1) {
            return;
        }
        const armory = [...forms].find(
            (f) => f.innerHTML.indexOf("Geschütze") >= 0
        );
        const categories = armory.querySelectorAll(".defenseCategory");
        let compactMode = false;
        for (let category of categories) {
            const rows = category.querySelectorAll("tr");
            if (rows.length > 0 && rows[0].childNodes.length > 5) {
                // compact view detected, no mobile adjustments available
                compactMode = true;
                break;
            }
            for (let i = 0; i < rows.length; i += 7) {
                const scheduleRow = rows[i + 3];
                scheduleRow.classList.add("defenseRowSchedule");
            }
        }
        const images = armory.querySelectorAll("img");
        for (let image of images) {
            image.classList.add(compactMode ? "defenseImageSmall" : "defenseImage");
            if (compactMode) {
                image.removeAttribute("width");
                image.removeAttribute("height");
            } else if (image.parentNode.nodeName === "A") {
                const anchor = image.parentNode;
                const cell = anchor.parentNode;
                anchor.style.display = "block";
                cell.removeAttribute("width");
                cell.removeAttribute("height");
                cell.style.verticalAlign = "top";
            } else if (image.parentNode.nodeName === "TD") {
                const cell = image.parentNode;
                image.style.display = "block";
                cell.removeAttribute("width");
                cell.removeAttribute("height");
                cell.style.verticalAlign = "top";
            }
        }
    }

    mountSectorMap() {
        const sectorMap = document.querySelector("#sector_map_table");
        if (sectorMap == null || true) {
            return;
        }

        const galaxyCells = [...sectorMap.querySelectorAll(".galaxyCell")];
        const numberCells = [...sectorMap.querySelectorAll(".galaxyCellNumber")];
        const referenceCell = galaxyCells[0];

        this.resolutionPostProcessors.push(() => {
            const referenceStyle = getComputedStyle(referenceCell);
            const cellSize = referenceStyle.width;
            if (parseInt(cellSize) <= 0) {
                return;
            }
            galaxyCells.forEach(c => c.style.height = cellSize);
            numberCells.forEach(c => c.style.height = cellSize);
        });

        sectorMap.offsetTop;
    }

    toggleMainMenu() {
        this.toggleMenu(this.mainMenu);
    }

    toggleTopMenu() {
        this.toggleMenu(this.topMenu);
    }

    togglePlanetDropdownMobile() {
        this.toggleMenu(this.planetDropdownMobile);
    }

    toggleMenu(menu) {
        const expand = menu.getAttribute("data-expanded") !== "true";
        menu.setAttribute("data-expanded", expand ? "true" : "false");
        if (expand) {
            this.blockInteractions(true);
            this.onNextDocumentClick(() => {
                menu.setAttribute("data-expanded", "false");
                this.blockInteractions(false);
            });
        }
    }

    onNextDocumentClick(callback) {
        const listener = () => {
            document.removeEventListener("click", listener);
            callback();
        };
        setTimeout(() => {
            document.addEventListener("click", listener);
        });
    }

    onScroll() {
        if (!this.isMobile && !this.isTablet) {
            return;
        }
        const scrollPosition = window.scrollY;
        const showMobileReloader =
            this.scrollStartPosition < 50 && scrollPosition < -100;
        const triggerReload =
            this.scrollStartPosition < 50 && scrollPosition < -150;
        const attachPlanetDropdown =
            scrollPosition >= this.planetSelectionMobileAttachPoint;
        if (attachPlanetDropdown !== this.planetSelectionMobileAttached) {
            this.planetSelectionMobileAttached = attachPlanetDropdown;
            if (attachPlanetDropdown) {
                this.planetSelectionMobile.classList.add("attached");
            } else {
                this.planetSelectionMobile.classList.remove("attached");
            }
        }
        if (showMobileReloader !== this.mobileReloaderVisible) {
            this.mobileReloaderVisible = showMobileReloader;
            this.mobileReloader.setAttribute(
                "data-visible",
                showMobileReloader ? "true" : "false"
            );
        }
        if (triggerReload && !this.reloadTriggered) {
            this.reloadTriggered = true;
            this.mobileReloader.setAttribute("data-triggered", "true");
            location.reload();
        }
    }

    blockInteractions(block) {
        this.element.setAttribute("data-interactable", block ? "false" : "true");
    }

    replacePixelSizes() {
        const elements = document.querySelectorAll("*[style]");
        const properties = ["width", "height", "top", "left", "bottom", "right", "font-size"];
        for (let element of elements) {
            for (let property of properties) {
                let factor = 1;
                if (property === "width" &&
                    (element.nodeName.toLowerCase() === "col" ||
                        element.nodeName.toLowerCase() === "th" ||
                        element.nodeName.toLowerCase() === "td")
                ) {
                    factor = 1.4;
                }
                const value = element.style[property];
                if (value != null && value.endsWith("px")) {
                    element.style[property] = this.convertPixelToRem(value, factor);
                }
                if (value != null && value.endsWith("pt")) {
                    element.style[property] = this.convertPointToRem(value, factor);
                }
            }
        }
    }

    convertPixelToRem(value, factor = 1) {
        return parseInt(value.substring(0, value.length - 2)) / 16 * factor + "rem";
    }

    convertPointToRem(value, factor = 1) {
        return parseInt(value.substring(0, value.length - 2)) / 8 * factor + "rem";
    }

    onTouchStart() {
        this.scrollStartPosition = window.scrollY;
        console.log(this.scrollStartPosition);

        if (this.element == null) {
            return;
        }

        if (this.inputMode !== "touch") {
            this.inputMode = "touch";
            this.element.classList.remove("mouse");

            const mouseMoveListener = () => {
                document.removeEventListener("mousemove", mouseMoveListener);
                this.inputMode = "mouse";
                if (!this.element.classList.contains("mouse")) {
                    this.element.classList.add("mouse");
                }
            };
            document.addEventListener("mousemove", mouseMoveListener);
        }
    }

    onWindowResize() {
        this.updateScaling();
    }

    updateScaling() {
        const screen = window.screen;
        const aspectRatio = screen.width / screen.height;
        const currentWidth = screen.width;
        const currentHeight = screen.height;

        const widthFactor = Math.min(1, currentWidth / REFERENCE_WIDTH);
        const heightFactor = Math.min(0.8, currentHeight / REFERENCE_Height);
        const heightFactorAdjusted = Math.max(
            aspectRatio > 2 ? widthFactor : 0,
            heightFactor
        );
        const isTablet = currentWidth <= 600;
        const isMobile = currentWidth <= 414;
        const isDesktop = currentWidth >= 1450;
        const mobileFactor = isMobile ? 1.75 : isTablet ? 1.5 : 1;
        const factor = isDesktop ? 0.67 : Math.min(widthFactor, heightFactorAdjusted) * mobileFactor;
        document.documentElement.style.fontSize = factor * 16 + 'px';
        this.isMobile = isMobile;
        this.isTablet = isTablet;

        if (this.resolutionPostProcessors != null) {
            this.resolutionPostProcessors.forEach(p => p());
        }
    }
}

const app = new App();
app.updateScaling();

document.addEventListener("DOMContentLoaded", () => {
    const appContainer = document.getElementById("app");
    if (appContainer == null) {
        console.log("App container not found.");
        return;
    }
    app.mount(appContainer);
});

window.design = "rediscovery";
