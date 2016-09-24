# typo3 basic configuration

## show page ids in backend pagetree
Backend users -> User -> Options -> TSConfig

```
options {
  pageTree.showPageIdWithTitle = 1
}
```

## activate extensions

- css styled content
- realurl
- vhs
- engine

## create a new page and make it a rootpage
edit page:

- General
  - Type -> Shortcut
  - Shortcut Mode -> First subpage of selected/current page
- Behaviour -> Use as root page
- Resources -> Page TSConfig `<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/Resources/Private/TypoScripts/Page.ts">`

### add template to rootpage
Template -> create template for new site (save and go to Info/Modify) -> edit the whole remplate record:

- General
  - Constants `<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/Resources/Private/TypoScripts/TemplateConstants.ts">`
  - Setup `<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/Resources/Private/TypoScripts/TemplateSetup.ts">`
- Options
  - [x] Constants
  - [x] Setup
  - [x] Rootlevel
- Includes
  - Include static (from extensions):
    - CSS Styled Content

### add backend layout to rootpage
Root page -> list view -> create new record -> Backend Layout

Config:

```
backend_layout {
  colCount = 1
  rowCount = 1
  rows {
    1 {
      columns {
        1 {
          name = Content
          colPos = 0
        }
      }
    }
  }
}
```

Edit rootpage -> Appearance:

- Backend Layout (this page only) -> select the layout
- Backend Layout (subpages of this page) -> select the layout

## add languages
see `typo3_page_localization.md`

## add composer deploy code to optimize performance

`composer dump-autoload --optimize`