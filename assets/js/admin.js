jQuery(document).ready(function(n){n("body").on("click",".toggle-new-venue",function(){var t=n(this).siblings(".venue-select"),e=n(this).siblings(".new-venue-input");e.is(":hidden")?(t.hide(),e.show().attr("name",t.attr("name")),t.removeAttr("name"),n(this).text("Use Existing Venue")):(t.show().attr("name",e.attr("name")),e.hide().removeAttr("name"),n(this).text("Add New Venue"))}),n("body").on("submit","form.compat-item",function(t){t.preventDefault();var e=n(this),i=e.find(".new-venue-input:visible"),u=e.find(".venue-select");if(i.length&&i.val()){var a=i.val();e.find('input[name="new_venue"]').length?e.find('input[name="new_venue"]').val(a):e.append('<input type="hidden" name="new_venue" value="'+a+'">'),u.val(a)}e[0].submit()})});
