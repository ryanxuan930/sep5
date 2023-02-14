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
  homepageSlideshow: string[];
  showCountdown: boolean;
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
  orgId: number;
}

export interface IRegConfig {
  game_id: number,
  reg_switch: number, //boolean
  deadline_list: {
    seperate: boolean;
    all: {
      start: string;
      end: string;
    };
    division: {
      start: string; 
      end: string;
      division_id: number
    }[];
  };
  options: {
    common: {
      min_reg_permission: number; // 最低報名權限
      allow_grouping: boolean; // 允許自行組隊->限制個人
      allow_cross_dept: boolean; // 允許跨分部組隊
      allow_cross_org: boolean; // 允許跨組織組隊
      individual: {
        max_event_per_athlete: number; // 每項目最多幾人報名
        max_athlete_per_event: number; // 每人最多報名幾項目
      };
      group: {
        max_event_per_team: number; // 每項目最多幾隊報名
        max_team_per_event?: number; // 每隊最多報名幾項目
      };
    };
    division: {
      division_id: number;
      prevent_sport_gifited: boolean; // 限非體優
      student_only: boolean; // 限學生報名
      has_grade: boolean;
      min_grade?: number;
      max_grade?: number;
    }[];
    event: {
      [key: string]: {
        prevent_sport_gifited: boolean;
        student_only: boolean;
        pre_define_member?: boolean;
        has_grade: boolean;
        min_grade?: number;
        max_grade?: number;
      }
    }
  }
}