<?php

/**
 * @psalm-type Preferences = array{designSets:array,customBreakpoints:Breakpoint[],customFonts:array}
 *
 * @psalm-type LauncherConfig = array{
 *  mode:"breakdance"|"wordpress",
 *  postType:string,
 *  builderLoaderUrl:string,
 *  isGutenberg:bool,
 *  canUseDefaultEditor:bool,
 *  hasFullAccess:bool,
 *  strings:array{
 *      description:string,
 *      openButton:string,
 *      disableButton:string,
 *      unsavedMessage:string
 * } }
 *
 * @psalm-type WPOEmbed = array{title: string, author_name: string, author_url: string, type: string, height: int, width: int, version: string, provider_name: string, provider_url: string, thumbnail_height: int, thumbnail_width: int, thumbnail_url: string, html: string}
 * @psalm-type OEmbed = array{title: string, provider: string, embedUrl: string|null, url: string, thumbnail: string, format: string, type: "oembed"|"video"}
 *
 * @psalm-type GoogleFontAxis = array{
 * tag: string,
 * start: float|int,
 * end: float|int
 * }
 *
 * @psalm-type BreakdanceFontVariants = GoogleFontAxis[]
 *
 * @psalm-type BreakdanceFont = array{slug:string,cssName:string,fallbackString:string,label:string,dependencies:ElementDependencyWithoutConditions,variants?:BreakdanceFontVariants}
 * @psalm-type OxygenVariable = array{id:string,cssVariableName:string,label:string,value:mixed,type:string,dynamicData:mixed,collection:string}
 * @psalm-type OxygenNestedSelector = array{id:string,name:string,pseudo?:bool,properties:array}
 * @psalm-type OxygenSelector = array{id:string,name:string,properties:array,type:string,children:OxygenNestedSelector[],collection:string}
 * @psalm-type OxygenSelectorCacheData = array{ id: string, hash: string, css: string, fonts?: string[] }
 * @psalm-type ElementPreset = array{id: string, name: string, availableForType: string, design: mixed}
 *
 * @psalm-type GoogleFont = array{
 * family: string,
 * variants: list<string>,
 * subsets: list<string>,
 * version: string,
 * lastModified: string,
 * files: array<string, string>,
 * category: string,
 * kind: string,
 * menu: string,
 * axes: GoogleFontAxis[]
 * }
 */
