// // Usage: https://github.com/Grsmto/simplebar

import SimpleBar from "simplebar";

const initialize = () => {
  initializeSimplebar();
  initializeSidebarCollapse();
  initializeSidebarLinkActive();
}

const initializeSimplebar = () => {
  const simplebarElement = document.querySelector(".js-simplebar");

  if(simplebarElement){
    const simplebarInstance = new SimpleBar(simplebarElement);

    // Recalculate simplebar on Bootstrap collapse show/hide
    const sidebarDropdowns = simplebarElement.querySelectorAll("[data-bs-parent]");

    sidebarDropdowns.forEach(dropdown => {
      dropdown.addEventListener("shown.bs.collapse", () => {
        simplebarInstance.recalculate();
      });
      dropdown.addEventListener("hidden.bs.collapse", () => {
        simplebarInstance.recalculate();
      });
    });
  }
}

const initializeSidebarCollapse = () => {
  const sidebarElement = document.querySelector(".js-sidebar");
  const sidebarToggleElement = document.querySelector(".js-sidebar-toggle");

  if(sidebarElement && sidebarToggleElement) {
    sidebarToggleElement.addEventListener("click", () => {
      sidebarElement.classList.toggle("collapsed");

      // Dispatch resize event after transition ends for charts/layouts
      sidebarElement.addEventListener("transitionend", () => {
        window.dispatchEvent(new Event("resize"));
      }, { once: true });
    });
  }
}

const initializeSidebarLinkActive = () => {
  const sidebarLinks = document.querySelectorAll(".js-sidebar .sidebar-link");

  // Highlight clicked link
  sidebarLinks.forEach(link => {
    link.addEventListener("click", function() {
      sidebarLinks.forEach(l => l.classList.remove("active"));
      this.classList.add("active");
    });
  });

  // On page load: highlight link matching current URL
  const currentUrl = window.location.pathname;

  sidebarLinks.forEach(link => {
    // Compare link href with current url
    if(link.pathname === currentUrl) {
      link.classList.add("active");

      // Also expand parents for nested dropdowns
      let parentCollapse = link.closest(".collapse");
      if(parentCollapse) {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(parentCollapse, { toggle: true });
        bsCollapse.show();

        // Also highlight the parent toggler (e.g. sidebar-link with dropdown)
        const parentToggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
        if(parentToggle) parentToggle.classList.add("active");
      }
    }
  });
}

// Wait until DOM loaded
document.addEventListener("DOMContentLoaded", () => initialize());



// import SimpleBar from "simplebar";

// const initialize = () => {
//   initializeSimplebar();
//   initializeSidebarCollapse();
// }

// const initializeSimplebar = () => {
//   const simplebarElement = document.getElementsByClassName("js-simplebar")[0];

//   if(simplebarElement){
//     const simplebarInstance = new SimpleBar(document.getElementsByClassName("js-simplebar")[0]);

//     /* Recalculate simplebar on sidebar dropdown toggle */
//     const sidebarDropdowns = document.querySelectorAll(".js-sidebar [data-bs-parent]");
    
//     sidebarDropdowns.forEach(link => {
//       link.addEventListener("shown.bs.collapse", () => {
//         simplebarInstance.recalculate();
//       });
//       link.addEventListener("hidden.bs.collapse", () => {
//         simplebarInstance.recalculate();
//       });
//     });
//   }
// }

// const initializeSidebarCollapse = () => {
//   const sidebarElement = document.getElementsByClassName("js-sidebar")[0];
//   const sidebarToggleElement = document.getElementsByClassName("js-sidebar-toggle")[0];

//   if(sidebarElement && sidebarToggleElement) {
//     sidebarToggleElement.addEventListener("click", () => {
//       sidebarElement.classList.toggle("collapsed");

//       sidebarElement.addEventListener("transitionend", () => {
//         window.dispatchEvent(new Event("resize"));
//       });
//     });
//   }
// }

// // Wait until page is loaded
// document.addEventListener("DOMContentLoaded", () => initialize());