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
if(dpiScale==null){
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

  mount(element) {
    this.element = element;
    this.gameArea = element.querySelector("#game-area");
    element.classList.add("mouse");
    document.body.addEventListener("touchstart", () => this.onTouchStart());
    window.addEventListener("scroll", () => this.onScroll());
    window.addEventListener("resize", () => this.onWindowResize());
    this.replacePixelSizes();
    this.updateScaling();
    this.mountMainNavigation();
    this.mountMobileNavigation();
    this.mountResourceBar();
    this.mountPlanetCircle();
    this.mountBuildOverview();
    this.mountShipyard();
    this.mountArmory();
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

  mountServerTime(element){
    const timeParts = element.innerText.split(":");
    if(timeParts.length < 3){
      console.warn("Failed to mount server time on element "+element);
      return;
    }
    const hour = parseInt(timeParts[0]);
    const minute = parseInt(timeParts[1]);
    const second = parseInt(timeParts[2]);

    const startDate = new Date();
    const referenceTime = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDay(), hour, minute, second);
    const offset = referenceTime.getTime() - startDate.getTime();

    const tickServertime = ()=>{
      if(!element){
        return;
      }
      const now = new Date((new Date()).getTime() - offset);
      element.innerText = now.toLocaleTimeString('de-DE');
      setTimeout(()=>{
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

    for (let image of planetCircle.querySelectorAll("img")) {
      image.removeAttribute("width");
      image.removeAttribute("height");
      image.classList.add("planetImage");
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
    for(let element of this.element.querySelectorAll(".buildOverviewObjectNone")){
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
    const categories = shipyard.querySelectorAll("table");
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
      if (compactMode) {
        image.removeAttribute("width");
        image.removeAttribute("height");
      }
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
    const categories = armory.querySelectorAll("table");
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
    const properties = ["width", "height", "top", "left", "bottom", "right"];
    for (let element of elements) {
      for (let property of properties) {
        const value = element.style[property];
        if (value != null && value.endsWith("px")) {
          element.style[property] = this.convertPixelToRem(value);
        }
      }
    }
  }

  convertPixelToRem(value) {
    return parseInt(value.substring(0, value.length - 2)) / 16 + "rem";
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
    const isDesktop = currentWidth >= 1024;
    const mobileFactor = isMobile ? 1.75 : isTablet ? 1.5 : 1;
    const factor = isDesktop ? 1 / DPI_SCALE : Math.min(widthFactor, heightFactorAdjusted) * mobileFactor;
    document.documentElement.style.fontSize = factor + "em";
    this.isMobile = isMobile;
    this.isTablet = isTablet;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const appContainer = document.getElementById("app");
  if (appContainer == null) {
    console.log("App container not found.");
    return;
  }
  const app = new App();
  app.mount(appContainer);
});

window.design = "rediscovery";
