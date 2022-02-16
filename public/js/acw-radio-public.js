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
  let list = document.getElementsByClassName("program-list");

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
        } ${slot.id === onAir.id && "onair"}"><div>${slot.program.name}  
        </div></a>`
      );

      $(`.weekday${tmp.weekday_start}`).append(
        `<div class='program-item-wrapper ${slot.id === onAir.id && "onair"}'>
			<div class='program-time'>${`${
        slot.hour_start >= 12 ? pad(slot.hour_start - 12) : pad(slot.hour_start)
      }:${pad(slot.minute_start)}`}${slot.hour_start >= 12 ? "pm" : "am"} </div>
			<div class='program-details'><a href="/program/${slot.program.slug}"><h4>${
          slot.id === onAir.id ? "<span class='online-alert'>ON AIR:</span> " : ""
        }${slot.program.name}</h4></a>
				${slot.program.genre_string ? `<p>${slot.program.genre_string}</p>` : ""}
				${slot.program.introduction ? `<p>${slot.program.introduction}</p>` : ""}
			</div>
			<div class='program-presenter'>${
        slot.program.presenter_string &&
        `${` <span>Presented by</span><br>${slot.program.presenter_string} `}`
      }</div>
			<div class='program-image'>${
        slot.program.image
          ? `<img src="${slot.program.image.url}" alt="${slot.program.name}" />`
          : ""
      }</div>
		</div>`
      );

      $(`.mobile_weekday_${slot.weekday_start}`).append(
        `<a href="/program/${slot.program.slug}" class="program_slot_mobile ${
          slot.id === onAir.id && "onair"
        }"><div><span class='mobile-times'>${`${pad(slot.hour_start)}:${pad(
          slot.minute_start
        )}`} to ${`${pad(slot.hour_end)}:${pad(slot.minute_end)}`}</span> 
		<div class='mobile-program-name'>${
      slot.id === onAir.id && "<span class='online-alert'>ON AIR:</span> "
    }${slot.program.name}</div> 
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
    $(".programguide").removeClass("loading");
  };

  if (station_vars) {
    if (guides.length > 0 || list) {
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

  function updateClick() {
    let dayNo = $(this).data("day");
    $(".weekday-toggle").removeClass("active");
    $(this).addClass("active");
    $(`.weekday-list`).removeClass("active");
    $(`.weekday${dayNo}`).addClass("active");
  }
  $(".weekday-toggle").on("click", updateClick);
})(jQuery);
