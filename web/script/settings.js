function getWidth() {
  if (self.innerWidth) {
    return self.innerWidth;
  }

  if (document.documentElement && document.documentElement.clientWidth) {
    return document.documentElement.clientWidth;
  }

  if (document.body) {
    return document.body.clientWidth;
  }
}

function setSettingsSize()
{
	//Set the size of the divs
	var settingsDivs = document.getElementById("settingsDiv");
	var menuSetting  = document.getElementById("menuSettings");

	var menuHeight = menuSetting.offsetHeight;
	var divHeight  = settingsDivs.offsetHeight;

	settingsDivs.style.height = ''+Math.max(menuHeight, divHeight);
	menuSetting.style.height = ''+Math.max(menuHeight, divHeight);

//	settingsDivs.style.width = getWidth() - settingsDivs.offsetLeft - settingsDivs. - 200;

	console.log(menuHeight);
	console.log(divHeight);
}
