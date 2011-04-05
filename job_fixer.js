/**
 * @author Robin
 */

var pricegroups = [
  {
    location_id: 2,
    ordertypes: [
      {oldtype: 20, newtype: 44}, // A1
      {oldtype: 21, newtype: 45}, // C1
      {oldtype: 22, newtype: 46}, // D1
      {oldtype: 23, newtype: 47}, // D4
      {oldtype: 50, newtype: 48}, // FA1
      {oldtype: 42, newtype: 49} // SV1 
    ]
  },
  {
    location_id: 9,
    ordertypes: [
      {oldtype: 20, newtype: 51}, // A1
      {oldtype: 21, newtype: 52}, // C1
      {oldtype: 22, newtype: 53}, // D1
      {oldtype: 23, newtype: 54}, // D4
      {oldtype: 50, newtype: 55}, // FA1
      {oldtype: 42, newtype: 56} // SV1 
    ]
  },
  {
    location_id: 8,
    ordertypes: [
      {oldtype: 20, newtype: 61}, // A1
      {oldtype: 21, newtype: 62}, // C1
      {oldtype: 22, newtype: 63}, // D1
      {oldtype: 23, newtype: 64}, // D4
      {oldtype: 50, newtype: 65}, // FA1
      {oldtype: 42, newtype: 66} // SV1 
    ]
  },
  {
    location_id: 6,
    ordertypes: [
      {oldtype: 20, newtype: 74}, // A1
      {oldtype: 21, newtype: 75}, // C1
      {oldtype: 22, newtype: 77}, // D1
      {oldtype: 23, newtype: 79}, // D4
      {oldtype: 50, newtype: 82}, // FA1
      {oldtype: 42, newtype: 81} // SV1 
    ]
  },
  {
    location_id: 3,
    ordertypes: [
      {oldtype: 20, newtype: 68}, // A1
      {oldtype: 21, newtype: 69}, // C1
      {oldtype: 22, newtype: 70}, // D1
      {oldtype: 23, newtype: 71}, // D4
      {oldtype: 50, newtype: 73}, // FA1
      {oldtype: 42, newtype: 72} // SV1 
    ]
  }
];

function find_type(location, oldtype) {
  for (var i = 0; i < pricegroups.length; i++) {
    var current = pricegroups[i];
    if (current.location_id == location) {
      for (var j = 0; j < current.ordertypes.length; j++) {
        var cur = current.ordertypes[j];
        if (cur.oldtype == oldtype) {
          return cur.newtype;
        }
      }
    }
  }
  
  return false;
}

var mysql = require('mysql');
var client = new mysql.Client();

client.user = "root";
client.password = "root";

client.connect(function() {
  console.log('connected');
  
  client.useDatabase('vpsnet', function() {
    start_fixes(function() {
      client.query("UPDATE ordertypes SET hidden=1 WHERE location_id=0", function() {
        console.log("all done");
        client.end();
      });
    });
  });
});

function start_fixes(cb) {
  var workorder_query = "SELECT workorders.location_id, ordertypes_workorders.* FROM ordertypes_workorders LEFT JOIN workorders ON (workorders.id = workorder_id)";
  
  client.query(workorder_query, function(err, results) {
    
    var next = function(index) {
      var current = results[index];
      if (current) {
        var newtype = find_type(current.location_id, current.ordertype_id);
      }
      
      if (newtype && current) {
        
        var update = "UPDATE ordertypes_workorders SET ordertype_id=? WHERE workorder_id=? AND ordertype_id=?";
        client.query(update, [newtype, current.workorder_id, current.ordertype_id], function(err) {
          if (!err) {
            console.log([newtype, current.workorder_id, current.ordertype_id]);
            
            if (index < results.length) {
              index++;
              next(index);
            } else {
              cb();
            }
          } else {
            console.log(err);
            index++;
            next(index);
          }
        });
      } else {
        if (index < results.length) {
          index++;
          next(index);
        } else {
          cb();
        }
      }
    };
    
    next(0);
  });
}
