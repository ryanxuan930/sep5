export interface IPostData {
  bulletin_id?: number;
  post_date: string;
  category: number;
  pinned: number;
  created_at: string;
  updated_at: string;
  post_by: number;
  clicks: number;
  related_game: number;
  related_admin_dept: number;
  multilingual: number;
  title_ch: string;
  title_en: string|null;
  content_ch: any;
  content_en: any;
  links: {title: string, url: string}[];
  release: number;
  game_name_ch?: string;
}

export interface IReturnData {
  status: string|number;
  message?: string|number;
  from?: string|number;
}

export interface IPageData {
  footerContent: string;
  homepageSlideshow: string[]|string;
  countdownTime: string;
  countdownTitle: string;
  registrationUrl: string;
  orgUrlTitle: {
    'zh-TW': string,
    'en-US': string,
  };
  orgUrl: string;
  firstNavbarBackgroundColor: string;
  secondNavbarBackgroundColor: string;
  logoImageUrl: string;
}