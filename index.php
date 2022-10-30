<!DOCTYPE html>
<html>
  <head>
    <title>side-to-side (w/ai)</title>
    <style>
      body,html{
        background: #000;
        margin: 0;
        height: 100vh;
        font-family: courier;
      }
      #c{
        width: 100%;
        height: 100%;
        position: absolute;
        background: #fff;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
      }
      .hidden{
        visibility: hidden;
      }
      #spinner{
        top: 0;
        pointer-events: none;
        left: 0;
        opacity: .25;
        position: absolute;
        width: 100vw;
        height: 100vh;
        z-index: 10;
        background-image: url(https://jsbot.cantelope.org/uploads/7ERcg.gif);
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
      }
    </style>
  </head>
  <body>
    <canvas id="c"></canvas>
    <div id="spinner"></div>
    <script>
      c=document.querySelector('#c')
      x=c.getContext('2d')
      S=Math.sin
      C=Math.cos
      Rn=Math.random
      R = function(r,g,b,a) {
        a = a === undefined ? 1 : a;
        return "rgba("+(r|0)+","+(g|0)+","+(b|0)+","+a+")";
      };
      t=go=0
      rsz=window.onresize=()=>{
        setTimeout(()=>{
          if(document.body.clientWidth > document.body.clientHeight*1.77777778){
            c.style.height = '100vh'
            setTimeout(()=>c.style.width = c.clientHeight*1.77777778+'px',0)
          }else{
            c.style.width = '100vw'
            setTimeout(()=>c.style.height = c.clientWidth/1.77777778 + 'px',0)
          }
          c.width=1920
          c.height=c.width/1.777777778
        },0)
      }
      rsz()

      Draw=()=>{
        if(!t){
          R=(Rl,Pt,Yw,m)=>{
            M=Math
            A=M.atan2
            H=M.hypot
            X=S(p=A(X,Y)+Rl)*(d=H(X,Y))
            Y=C(p)*d
            X=S(p=A(X,Z)+Yw)*(d=H(X,Z))
            Z=C(p)*d
            Y=S(p=A(Y,Z)+Pt)*(d=H(Y,Z))
            Z=C(p)*d
            if(m){
              X+=oX
              Y+=oY
              Z+=oZ
            }
          }
          Q=()=>[c.width/2+X/Z*800,c.height/2+Y/Z*800]
          I=(A,B,M,D,E,F,G,H)=>(K=((G-E)*(B-F)-(H-F)*(A-E))/(J=(H-F)*(M-A)-(G-E)*(D-B)))>=0&&K<=1&&(L=((M-A)*(B-F)-(D-B)*(A-E))/J)>=0&&L<=1?[A+K*(M-A),B+K*(D-B)]:0
          stroke=(scol,fcol)=>{
            if(scol){
              x.closePath()
              x.lineWidth=Math.min(500, 500/Z)
              x.globalAlpha=.2
              x.strokeStyle=scol
              x.stroke()
              x.globalAlpha=1
              x.lineWidth/=5
              x.stroke()
            }
            if(fcol){
              x.fillStyle=fcol
              x.fill()
            }
          }
          spawnBoard=()=>{
            stage = gameover = 0
            begCol=-1
            sp=2, mx=my=turn=0
            B=Array(100).fill().map((v,i)=>{
              X=((i%10)-5+.5)*sp
              Y=((i/10|0)-5+.5)*sp
              Z=0
              return [X,Y,Z,i]
            })

            P=Array(122).fill().map((v,i)=>{
              ret = -1
              if(i>0&&(i<121)&&(i%2)){
                val = (((i+(i/11|0))|0))%2?3:2
                ret = val
              }
              return ret
            })
          }

          c.onmousedown=e=>{
            switch(stage){
              case 0:
                if(!gameover && e.button==0 && begCol != -1){
                  turn=begCol
                  stage++
                }
              break
              case 1:
                if(e.button==0){
                  if(gameover) spawnBoard()
                  if(P[tgt_]==-1){
                    turn=(turn+1)%2
                    P[tgt_]=turn
                  }
                }
            }
          }
          c.onmousemove=e=>{
            let rect = c.getBoundingClientRect()
            mx=(e.pageX-rect.left)/c.clientWidth*c.width
            my=(e.pageY-rect.top)/c.clientHeight*c.height
          }
          bgimg = new Image()
          bgimg.onload=()=>{
            go=true
            spawnBoard()
            hideSpinner()
          }
          //bgimg.src='/proxy.php?url=https://jsbot.cantelope.org/uploads/1s3j4w.jpg'
          //bgimg.src='/proxy.php?url=https://jsbot.cantelope.org/uploads/1NWwfr.jpg'
          bgimg.src='/proxy.php?url=https://jsbot.cantelope.org/uploads/1bUCwu.jpg'

          showSpinner=()=>{
            let el=document.querySelector('#spinner')
            if(el.classList.contains('hidden')) el.classList.toggle("hidden")
          }

          hideSpinner=()=>{
            let el=document.querySelectorAll('#spinner')[0]
            if(!el.classList.contains('hidden')) el.classList.toggle("hidden")
          }

          doAIMove=()=>{
            showSpinner()
            setTimeout(()=>{
              let moved = false
              tgt_=-1

              let maxDepth = 2
              let victor=-1, tcell

              forecast = (P, depth) => {
                if(depth > maxDepth || victor != -1) return
                for(let i=122;i--;){
                  if(victor==-1 && P[i]==-1 && (!(i%2))&&((i/11|0)>0)&&(i%11!==10)&&(i%11)&&((i/11|0)<10)){
                    let hP=JSON.parse(JSON.stringify(P))
                    hP[i]=turn
                    for(let j=5;j--;){
                      good=false
                      memo=Array(122).fill(false)
                      if(!begCol){
                        recursepurple(1+j*2, hP)
                      }else{
                        recursegreen(11+11*j*2, hP)
                      }
                      if(good) victor=!turn, tcell=i
                    }
                    if(victor==-1){
                      forecast(hP, depth+1)
                    }
                  }
                }
              }
              forecast(P, 0)

              // opponent is not 1 move away from victory
              if(victor==-1){
                let ct=0
                do{
                  i=Rn()*122|0
                  if(!moved && P[i]==-1 && (!(i%2))&&((i/11|0)>0)&&(i%11!==10)&&(i%11)&&((i/11|0)<10)){
                    tgt_=i
                    moved = true
                  }
                  ct++
                }while(!moved && ct<200);
                if(P[tgt_]==-1){
                  turn=(turn+1)%2
                  P[tgt_]=turn
                }
              } else { // else block
                turn=(turn+1)%2
                P[tcell]=turn        
              }
              hideSpinner()
            },0)
          }
        }

        if(go){
          x.globalAlpha=1
          x.drawImage(bgimg,0,0,c.width,c.height)
          x.globalAlpha=1
          x.fillStyle='#000a'
          x.fillRect(0,0,c.width,c.height)
          x.lineCap=x.lineJoin='round'
          oX=oY=0, oZ=20
          Rl=0
          Pt=gameover?C(t/2)*10:0
          Yw=gameover?S(t/2)*10:0

          B.map(v=>{
            tx=v[0]
            ty=v[1]
            tz=v[2]
            ls=sp*(2**.5/2)
            x.beginPath()
            for(i=4;i--;){
              X=tx+S(p=Math.PI*2/4*i+Math.PI/4)*ls
              Y=ty+C(p)*ls
              Z=tz
              R(Rl,Pt,Yw,1)
              if(Z>0) x.lineTo(...Q())
            }
            stroke('#fff2','')
          })

          for(i=110;i--;){
            if(i%10<9&&!(i%2)&&!((i/10|0)%2)){
              X=((i%10)-5+.5)*sp+sp/2
              Y=((i/10|0)-5+.5)*sp-sp/2
              Z=0
              x.beginPath()
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 300/Z)
              x.arc(...Q(),s,0,7)
              x.fillStyle='#80f'
              x.fill()

              X=((i/10|0)-5+.5)*sp-sp/2
              Y=((i%10)-5+.5)*sp+sp/2
              Z=0
              x.beginPath()
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 300/Z)
              x.arc(...Q(),s,0,7)
              x.fillStyle='#0f8'
              x.fill()
            }
          }
          l=2**.5*5*sp*1.01
          for(i=4;i--;){
            x.beginPath()
            x.lineWidth=Math.min(500, 600/Z)
            x.strokeStyle=i%2?'#80f8':'#0f88'
            X=S(p=Math.PI*2/4*i+Math.PI/4)*l
            Y=C(p)*l
            Z=0
            R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            X=S(p=Math.PI*2/4*(i+1)+Math.PI/4)*l
            Y=C(p)*l
            Z=0
            R(Rl,Pt,Yw,1)
            x.lineTo(...Q())
            x.stroke()
            x.lineWidth/=3
            x.strokeStyle=i%2?'#80f':'#0f8'
            x.stroke()
          }
          switch(stage){
            case 0:
              l_=[]
              x.font='90px courier'
              x.textAlign='center'
              x.fillStyle='#fff'
              x.fillText('choose a color to go first',960,70)
              x.beginPath()
              X=-16, Y=0, Z=0
              R(Rl,Pt,Yw,1)
              s=Math.min(500, 2e3/Z)
              x.arc(...(l_[0]=Q()),s,0,7)
              x.fillStyle='#80f'
              x.fill()
              x.strokeStyle='#fff8'
              ga=t*200%200
              x.beginPath()
              x.arc(...Q(),s+ga,0,7)
              x.globalAlpha=1/(1+ga**3/99999)
              x.lineWidth=Math.min(500,500/Z)
              x.stroke()
              X=16, Y=0, Z=0
              R(Rl,Pt,Yw,1)
              x.beginPath()
              x.arc(...Q(),s+ga,0,7)
              x.globalAlpha=1/(1+ga**3/99999)
              x.lineWidth=Math.min(500,500/Z)
              x.stroke()
              x.globalAlpha=1
              x.beginPath()
              s=Math.min(500, 2e3/Z)
              x.arc(...(l_[1]=Q()),s,0,7)
              x.fillStyle='#0f8'
              x.fill()
              
              for(let m=2;m--;){
                v=l_[m]
                d=Math.hypot(v[0]-mx,v[1]-my)
                if(d<200){
                  begCol=m
                  x.beginPath()
                  x.strokeStyle='#0f8'
                  s=120
                  x.arc(...v,s,0,7)
                  x.lineWidth=1e3/Z
                  x.stroke()
                }
              }
            break
            case 1:
              tgt_=-1
              for(i=122;i--;){
                if((!(i%2))&&((i/11|0)>0)&&(i%11!==10)&&(i%11)&&((i/11|0)<10)){
                  X=((i%11)-5.5+.5)*sp
                  Y=((i/11|0)-5.5+.5)*sp
                  Z=0
                  R(Rl,Pt,Yw,1)
                  tgt=Q()
                  d=Math.hypot(tgt[0]-mx,tgt[1]-my)
                  if(d<sp*25){
                    x.beginPath()
                    s=Math.min(500, 500/Z)
                    x.arc(...Q(),s,0,7)
                    x.fillStyle='#0f0'
                    if(P[i]==-1)x.fill()
                    tgt_=i
                  }
                }
              }
              P.map((v,i)=>{
                if(v!=-1&&v<2){
                  if((v&&((i/11|0)%2))||(!v&&(!((i/11|0)%2)))){
                    x.fillStyle=v?'#80f8':'#0f8a'
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.beginPath()
                    s=Math.min(500,750/Z)
                    x.arc(...Q(),s,0,7)
                    x.fill()
                    x.beginPath()
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp-sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp+sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    x.lineWidth=Math.min(500,500/Z)
                    x.strokeStyle=v?'#84fc':'#0f8c'
                    x.stroke()
                  }else{
                    x.fillStyle=v?'#80f8':'#0f8a'
                    X=((i%11)-5.5+.5)*sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.beginPath()
                    s=Math.min(500,750/Z)
                    x.arc(...Q(),s,0,7)
                    x.fill()
                    x.beginPath()
                    X=((i%11)-5.5+.5)*sp-sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    X=((i%11)-5.5+.5)*sp+sp
                    Y=((i/11|0)-5.5+.5)*sp
                    Z=0
                    R(Rl,Pt,Yw,1)
                    x.lineTo(...Q())
                    x.lineWidth=Math.min(500,500/Z)
                    x.strokeStyle=v?'#84fc':'#0f8c'
                    x.stroke()
                  }
                }
              })
              recursepurple=(idx, P)=>{
                if(P[idx]!==1 && P[idx]!=3) return
                if(idx>120 || idx<1) return
                if(memo[idx]) return
                if(idx>100){
                  good = true
                  return
                }
                memo[idx]=true
                let X=idx%11
                let Y=idx/11|0
                recursepurple(idx+1, P)
                recursepurple(idx-1, P)
                recursepurple(idx+11, P)
                recursepurple(idx-11, P)
              }
              for(let j=5;j--;){
                good=false
                memo=Array(122).fill(false)
                recursepurple(1+j*2, P)
                if(good) gameover=true, victor=1
              }

              //check green victory
              recursegreen=(idx, P)=>{
                if(P[idx]!==0 && P[idx]!=2) return
                if(idx>120 || idx<1) return
                if(memo[idx]) return
                if(idx%11==10){
                  good = true
                  return
                }
                memo[idx]=true
                let X=idx%11
                let Y=idx/11|0
                recursegreen(idx+1, P)
                recursegreen(idx-1, P)
                recursegreen(idx+11, P)
                recursegreen(idx-11, P)
              }
              for(let j=5;j--;){
                good=false
                memo=Array(122).fill(false)
                recursegreen(11+11*j*2, P)
                if(good) gameover=true, victor=0
              }

              if(!gameover){
                x.font='90px courier'
                x.textAlign='center'
                x.fillStyle=turn?'#0f8':'#80f'
                x.fillText((turn?'green':'purple')+'\'s'+' turn',960,80)
                if((turn+begCol)%2) doAIMove()
              } else {
                x.globalAlpha=1
                x.font='200px courier'
                x.textAlign='center'
                x.fillStyle=!victor?'#0f8':'#80f'
                x.strokeStyle=!victor?'#8fc8':'#a6f8'
                x.lineWidth=20
                x.strokeText((!victor?'green':'purple')+' WON!',960,500)
                x.fillText((!victor?'green':'purple')+' WON!',960,500)
                x.fillStyle='#fff'
                x.font='120px courier'
                x.fillText('click to play again...',960,640)
              }
            break
          }
        } else {
          x.fillStyle='#0008'
          x.fillRect(0,0,c.width,c.height)
        }

        t+=1/60
        requestAnimationFrame(Draw)

      }
      Draw()
    </script>
  </body>
</html>
