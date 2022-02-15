let guide = [];
let programs = [];

const weekDays = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

(function ($) {
  "use strict";
  let guides = document.getElementsByClassName("programguide");
  let onairs = document.getElementsByClassName("onairnow");
  console.log(onairs);
  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  const getOnAir = async () => {
    const response = await fetch(
      `http://127.0.0.1:8000/api/public/station/${station_vars.api_key}/onair`
    );
    const results = await response.json();
    console.log("on air", results);

	for (let index = 0; index < onairs.length; index++) {
		const element = onairs[index];
		let newTitle = document.createElement("h4");
		newTitle.append(`${results.data.slot.program.name}`);
		element.append(newTitle);
		element.classList.remove("loading");
	}

  };

  const getGuide = async () => {
    const response = await fetch(
      `http://127.0.0.1:8000/api/public/station/${station_vars.api_key}`
    );
    const results = await response.json();
    let days = [];
    for (let index = 0; index < 7; index++) {
      days[index] = results.data.slots.filter(
        (item) => item.weekday_start === index
      );
    }
    const parent = document.getElementsByClassName("programguide");
    for (let index = 0; index < 7; index++) {
      if (days[index].length > 0) {
        let child = document.createElement("div");
        let title = document.createElement("h3");
        title.append(`${weekDays[index]}`);
        child.append(title);
        child.classList.add(`weekday-${index}`);
        child.classList.add(`weekday-${weekDays[index]}`);
        for (let i2 = 0; i2 < days[index].length; i2++) {
          const element = days[index][i2];
          console.log(element);
          let newElement = await populateDay(days[index][i2]);
          child.append(newElement);
        }
        parent[0].appendChild(child);
      }
    }
    parent[0].classList.remove("loading");
  };

  if (station_vars) {
    if (guides.length > 0) {
      getGuide();
    } else {
      console.log("Guide not found");
    }
    if (onairs.length > 0) {
      getOnAir();
    } else {
      console.log("OnAir not found");
    }
  }
  const pad = (d) => {
    return d < 10 ? "0" + d.toString() : d.toString();
  };

  const populateDay = async (content) => {
    let child = document.createElement("div");
    child.classList.add("program-slot");
    child.classList.add(content.program.slug);
    child.classList.add(`seconds-${content.seconds_from_sunday}`);
    child.classList.add(`weekdaystart-${content.weekday_start}`);
    child.classList.add(`weekdayend-${content.weekday_end}`);
    child.classList.add(`hourstart-${content.hour_start}`);
    child.classList.add(`hourend-${content.hour_end}`);
    child.classList.add(`minutestart-${content.minute_start}`);
    child.classList.add(`minuteend-${content.minute_end}`);
    child.classList.add(`weekdaystart-${content.weekday_start}`);
    child.classList.add(`weekdayend-${content.weekday_end}`);
    child.classList.add(`duration-${content.duration}`);
    let title = document.createElement("div");
    let start = document.createElement("div");
    let end = document.createElement("div");
    title.append(content.program.name);
    start.append(
      `${
        content.hour_start <= 12
          ? pad(content.hour_start)
          : pad(content.hour_start - 12)
      }:${pad(content.minute_start)}${content.hour_start <= 12 ? "am" : "pm"}`
    );
    end.append(
      `${
        content.hour_end <= 12
          ? pad(content.hour_end)
          : pad(content.hour_end - 12)
      }:${pad(content.minute_end)}${content.hour_end <= 12 ? "am" : "pm"}`
    );
    child.append(start);
    child.append(end);
    child.append(title);
    return child;
  };
})(jQuery);
