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
  console.log(station_vars);
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

  const sortOutOffset = (thisSlot) => {
    let newSlot = { ...thisSlot };
    let weekday = thisSlot.weekday_start;
    let start = thisSlot.hour_start;
    start = start - parseInt(station_vars.offset);
    if (start > 23) {
      start = start - 24;
      weekday++;
    }
    if (start < 0) {
      start = start + 24;
      weekday--;
    }
    if (weekday > 6) {
      weekday = 0;
    }
    if (weekday < 0) {
      weekday = 6;
    }

    return { ...thisSlot, hour_start: start, weekday_start: weekday };
  };

  const getOnAir = async () => {
    const response = await fetch(
      `https://app.myradio.click/api/public/station/${station_vars.api_key}/onair`
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
      `https://app.myradio.click/api/public/station/${station_vars.api_key}/guide`
    );
    const results = await response.json();
    let tmp;

    let onAir = results.data.onair.slot;

    results.data.guide.forEach((slot) => {
      tmp = sortOutOffset(slot);

      $(
        `.${tmp.weekday_start}_hour_${tmp.hour_start}_${slot.minute_start}`
      ).append(
        `<a href="/program/${slot.program.slug}" class="program_slot height_${
          slot.duration / 60
        } ${slot.id === onAir.id && "onair"}"><div>${slot.program.name} ${
          slot.program.presenter_string &&
          `<br /><span class='grid-presenter'>${slot.program.presenter_string}</span>`
        }</div></a>`
      );

      $(`.mobile_weekday_${slot.weekday_start}`).append(
        `<a href="/program/${
          slot.program.slug
        }" class="program_slot_mobile ${slot.id === onAir.id && "onair"}"><div><span class='mobile-times'>${`${pad(
          slot.hour_start
        )}:${pad(slot.minute_start)}`} to ${`${pad(slot.hour_end)}:${pad(
          slot.minute_end
        )}`}</span> 
		<div class='mobile-program-name'>${slot.id === onAir.id && "<span class='online-alert'>ON AIR:</span> "}${slot.program.name}</div> 
		${
      slot.program.presenter_string &&
      `${`<span class='mobile-presenter'>Presented by ${slot.program.presenter_string}</span>`}`
    }</div></a>`
      );
    });
    let days = [];
    for (let index = 0; index < 7; index++) {
      days[index] = results.data.guide.filter(
        (item) => item.weekday_start === index
      );
    }

    // for (let index = 0; index < 7; index++) {
    //   if (days[index].length > 0) {
    //     let child = document.createElement("div");
    //     let title = document.createElement("h3");
    //     title.append(`${weekDays[index]}`);
    //     child.append(title);
    //     child.classList.add(`weekday-${index}`);
    //     child.classList.add(`weekday-${weekDays[index]}`);
    //     for (let i2 = 0; i2 < days[index].length; i2++) {
    //       const element = days[index][i2];
    //       console.log(element);
    //       let newElement = await populateDay(days[index][i2]);
    //       child.append(newElement);
    //     }
    //     parent[0].appendChild(child);
    //   }
    // }
    // $(".programguide").removeClass("loading");
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

  //   const populateDay = async (content) => {
  //     let child = document.createElement("div");
  //     child.classList.add("program-slot");
  //     child.classList.add(content.program.slug);
  //     child.classList.add(`seconds-${content.seconds_from_sunday}`);
  //     child.classList.add(`weekdaystart-${content.weekday_start}`);
  //     child.classList.add(`weekdayend-${content.weekday_end}`);
  //     child.classList.add(`hourstart-${content.hour_start}`);
  //     child.classList.add(`hourend-${content.hour_end}`);
  //     child.classList.add(`minutestart-${content.minute_start}`);
  //     child.classList.add(`minuteend-${content.minute_end}`);
  //     child.classList.add(`weekdaystart-${content.weekday_start}`);
  //     child.classList.add(`weekdayend-${content.weekday_end}`);
  //     child.classList.add(`duration-${content.duration}`);
  //     let title = document.createElement("div");
  //     let start = document.createElement("div");
  //     let end = document.createElement("div");
  //     title.append(content.program.name);
  //     start.append(
  //       `${
  //         content.hour_start <= 12
  //           ? pad(content.hour_start)
  //           : pad(content.hour_start - 12)
  //       }:${pad(content.minute_start)}${content.hour_start <= 12 ? "am" : "pm"}`
  //     );
  //     end.append(
  //       `${
  //         content.hour_end <= 12
  //           ? pad(content.hour_end)
  //           : pad(content.hour_end - 12)
  //       }:${pad(content.minute_end)}${content.hour_end <= 12 ? "am" : "pm"}`
  //     );
  //     child.append(start);
  //     child.append(end);
  //     child.append(title);
  //     return child;
  //   };
})(jQuery);
