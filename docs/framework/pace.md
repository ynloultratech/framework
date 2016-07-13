## Pace
[Pace](github.hubspot.com/pace/docs/welcome/) is a automatic page load progress bar and is enabled by default when the YnloFrameworkBundle is added to the kernel. 

### Installation

- Add base framework assets `@ynlo_framework_all_css` and `@ynlo_framework_all_js` to your template. ([Adding Assets](assets.md)) 

### Settings:

_config.yml:_
````yml
ynlo_framework:
    pace: false
    assets:
        pace_js: '.../pace.js'
        pace_css: '.../pace.css'
````

 